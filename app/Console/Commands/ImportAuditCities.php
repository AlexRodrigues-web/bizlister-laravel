<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportAuditCities extends Command
{
    protected $signature = 'import:audit-cities {--limit=10 : Limite de exemplos na prévia}';
    protected $description = 'Audita charset/collation, mojibake e pendências de business.sid. NÃO altera nada.';

    protected array $mojiNeedles = ['Ã', 'Â', '�'];

    public function handle(): int
    {
        $this->info('=== Audit: Cidades / Charset / Mojibake / SID (READ-ONLY) ===');

        $this->auditDatabaseCharset();
        $this->auditTablesCharset(['business','category','city']);
        $this->auditMojibake();
        $this->auditSidGapsAndPreview((int)$this->option('limit'));
        $this->auditCityDuplicates();

        $this->line('');
        $this->comment('Fim da auditoria. Nenhuma alteração foi feita.');
        $this->line('> Próximos passos sugeridos (apenas se necessário):');
        $this->line('  - Normalizar mojibake nas colunas que apresentaram contagens > 0;');
        $this->line('  - Resolver duplicatas de cidades com o mesmo nome normalizado;');
        $this->line('  - Popular business.sid a partir de business.city (após resolver duplicatas).');

        return self::SUCCESS;
    }

    /* ===================== Charset / Collation ===================== */

    protected function auditDatabaseCharset(): void
    {
        $row = DB::selectOne("
            SELECT 
              @@character_set_database AS charset_db,
              @@collation_database     AS coll_db
        ");
        $this->line('');
        $this->info('> Database charset/collation');
        $this->table(
            ['character_set_database','collation_database'],
            [[ $row->charset_db ?? '-', $row->coll_db ?? '-' ]]
        );
    }

    protected function auditTablesCharset(array $tables): void
    {
        $this->info('> Tabelas: charset/collation');
        $data = [];
        foreach ($tables as $t) {
            $row = DB::selectOne("
                SELECT 
                  T.TABLE_NAME          AS table_name,
                  T.TABLE_COLLATION     AS table_collation,
                  CCSA.CHARACTER_SET_NAME AS character_set
                FROM information_schema.TABLES T
                JOIN information_schema.COLLATION_CHARACTER_SET_APPLICABILITY CCSA
                  ON CCSA.COLLATION_NAME = T.TABLE_COLLATION
                WHERE T.TABLE_SCHEMA = DATABASE()
                  AND T.TABLE_NAME = ?
            ", [$t]);
            $data[] = [
                'table'      => $t,
                'charset'    => $row->character_set ?? '-',
                'collation'  => $row->table_collation ?? '-',
            ];
        }
        $this->table(['table','charset','collation'], $data);
    }

    /* ========================= Mojibake ============================ */

    protected function auditMojibake(): void
    {
        $this->info('> Mojibake (ocorrências por coluna)');

        $checks = [
            ['table' => 'category', 'col' => 'category'],
            ['table' => 'city',     'col' => 'city'],
            ['table' => 'business', 'col' => 'business_name'],
            ['table' => 'business', 'col' => 'description'],
            ['table' => 'business', 'col' => 'city'],
        ];

        $rows = [];
        foreach ($checks as $c) {
            $cnt = $this->countMojibake($c['table'], $c['col']);
            $rows[] = [$c['table'], $c['col'], $cnt];
        }
        $this->table(['table','column','suspects_count'], $rows);
    }

    protected function countMojibake(string $table, string $col): int
    {
        $wheres = [];
        $bindings = [];
        foreach ($this->mojiNeedles as $needle) {
            $wheres[] = "$col LIKE ?";
            $bindings[] = "%{$needle}%";
        }
        $sql = "SELECT COUNT(*) AS c FROM {$table} WHERE (".implode(' OR ', $wheres).")";
        $res = DB::selectOne($sql, $bindings);
        return (int)($res->c ?? 0);
    }

    /* =================== SID gaps + preview match =================== */

    protected function auditSidGapsAndPreview(int $limit = 10): void
    {
        $this->info('> business.sid pendentes (sid NULL/0 com business.city preenchido)');

        $countRow = DB::selectOne("
            SELECT COUNT(*) AS cnt
            FROM business
            WHERE (sid IS NULL OR sid = 0)
              AND city IS NOT NULL AND city <> ''
        ");
        $this->line('Pendentes: '.((int)$countRow->cnt));

        if ((int)$countRow->cnt === 0) {
            return;
        }

        // Mapa city normalizada -> city_id
        $cityRows = DB::table('city')->select('city_id','city')->get();
        $map = [];
        foreach ($cityRows as $c) {
            $norm = $this->norm((string)$c->city);
            if ($norm !== '') $map[$norm] = (int)$c->city_id;
        }

        // Amostra de negócios pendentes com prévia de match
        $sample = DB::table('business')
            ->select('biz_id','business_name','city')
            ->where(function($q){
                $q->whereNull('sid')->orWhere('sid',0);
            })
            ->whereNotNull('city')
            ->where('city','<>','')
            ->limit($limit)
            ->get();

        $preview = [];
        foreach ($sample as $r) {
            $norm = $this->norm((string)$r->city);
            $matched = $map[$norm] ?? null;
            $preview[] = [
                'biz_id'        => $r->biz_id,
                'business_name' => Str::limit((string)$r->business_name, 40),
                'city_raw'      => (string)$r->city,
                'norm(city)'    => $norm,
                'match_city_id' => $matched ? (string)$matched : '(nenhum)',
            ];
        }

        $this->table(['biz_id','business_name','city','norm(city)','match_city_id'], $preview);
    }

    /* ====================== Duplicatas de city ====================== */

    protected function auditCityDuplicates(): void
    {
        $this->info('> Duplicatas de city por nome normalizado (podem atrapalhar o match)');

        $rows = DB::table('city')
            ->select('city_id','city')
            ->get();

        $bucket = [];
        foreach ($rows as $r) {
            $norm = $this->norm((string)$r->city);
            $bucket[$norm][] = $r;
        }

        $dups = [];
        foreach ($bucket as $norm => $list) {
            if ($norm === '' || count($list) < 2) continue;
            $dups[] = [
                'norm(city)' => $norm,
                'city_ids'   => implode(',', array_map(fn($o)=>$o->city_id, $list)),
                'variants'   => implode(' | ', array_map(fn($o)=>$o->city, $list)),
            ];
        }

        if (empty($dups)) {
            $this->line('Sem duplicatas por nome normalizado. ✅');
            return;
        }

        // Mostra até 50 grupos para não poluir
        $this->table(['norm(city)','city_ids','variants'], array_slice($dups, 0, 50));
        if (count($dups) > 50) {
            $this->comment('... há mais duplicatas (mostrando primeiras 50).');
        }
    }

    /* ========================= Helpers ============================== */

    protected function norm(string $s): string
    {
        // trim + minúsculas
        $s = Str::lower(trim($s));

        // remover acentuação (Stringable) e converter para string
        $s = (string) Str::of($s)->ascii('pt');

        // comprimir espaços múltiplos em 1 e aparar
        $s = trim((string) preg_replace('/\s+/', ' ', $s));

        return $s;
    }
}
