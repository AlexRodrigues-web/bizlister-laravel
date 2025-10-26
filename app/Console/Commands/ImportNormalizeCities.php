<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportNormalizeCities extends Command
{
    protected $signature = 'import:normalize-cities 
        {--apply : Executa UPDATEs; sem esta flag faz apenas dry-run}
        {--limit=50 : Quantos exemplos mostrar por coluna (dry-run)}';

    protected $description = 'Normaliza strings com mojibake em tabelas legado. Por padrão é dry-run; use --apply para gravar.';

    // Mapeamento de mojibake comum -> UTF-8 correto
    protected array $map = [
        // minúsculas
        "Ã¡"=>"á","Ã "=>"à","Ã¢"=>"â","Ã£"=>"ã","Ã¤"=>"ä",
        "Ã©"=>"é","Ã¨"=>"è","Ãª"=>"ê","Ã«"=>"ë",
        "Ã­"=>"í","Ã¬"=>"ì","Ã®"=>"î","Ã¯"=>"ï",
        "Ã³"=>"ó","Ã²"=>"ò","Ã´"=>"ô","Ãµ"=>"õ","Ã¶"=>"ö",
        "Ãº"=>"ú","Ã¹"=>"ù","Ã»"=>"û","Ã¼"=>"ü",
        "Ã§"=>"ç","Ã±"=>"ñ",

        // MAIÚSCULAS (sequências corretas)
        "Ã"=>"Á","Ã"=>"À","Ã"=>"Â","Ã"=>"Ã","Ã"=>"Ä",
        "Ã"=>"É","Ã"=>"È","Ã"=>"Ê","Ã"=>"Ë",
        "Ã"=>"Í","Ã"=>"Ì","Ã"=>"Î","Ã"=>"Ï",
        "Ã"=>"Ó","Ã"=>"Ò","Ã"=>"Ô","Ã"=>"Õ","Ã"=>"Ö",
        "Ã"=>"Ú","Ã"=>"Ù","Ã"=>"Û","Ã"=>"Ü",
        "Ã"=>"Ç","Ã"=>"Ñ",

        // pontuação/símbolos comuns
        "â"=>"’","â"=>"–","â"=>"—","â¦"=>"…","â"=>"“","â"=>"”",
        "â¢"=>"•","â¬"=>"€",

        // ruído comum
        "Â"=>"",
        "�"=>""
    ];

    // Colunas alvo: [tabela, coluna]
    protected array $targets = [
        ['category','category'],
        ['city','city'],
        ['business','business_name'],
        ['business','description'],
        ['business','city'],
    ];

    public function handle(): int
    {
        $apply = (bool)$this->option('apply');
        $limit = (int)$this->option('limit');

        $this->info($apply ? '=== NORMALIZE (APLICANDO) ===' : '=== NORMALIZE (DRY-RUN) ===');

        foreach ($this->targets as [$table,$col]) {
            $this->line('');
            $this->info("> {$table}.{$col}");

            // Seleciona linhas suspeitas
            $suspects = $this->suspectQuery($table, $col, $limit + 1)->get();
            $countAll = $this->suspectQuery($table, $col)->count();

            $this->line("Suspeitos totais: {$countAll}");

            if ($suspects->isEmpty()) {
                continue;
            }

            // Prévia
            $preview = [];
            foreach ($suspects->take($limit) as $row) {
                $old = (string)$row->$col;
                $new = $this->fix($old);
                if ($old !== $new) {
                    $preview[] = [
                        'id'   => $row->id ?? ($row->biz_id ?? ($row->cat_id ?? ($row->city_id ?? '?'))),
                        'old'  => mb_strimwidth($old, 0, 80, '…', 'UTF-8'),
                        'new'  => mb_strimwidth($new, 0, 80, '…', 'UTF-8'),
                    ];
                }
            }
            if ($preview) {
                $this->table(['id','old','new'], $preview);
            }

            if ($apply && $countAll > 0) {
                // UPDATE em lote usando REPLACE aninhado
                $expr = $this->buildSqlReplaceChain($col);
                $sql  = "UPDATE {$table} SET {$col} = {$expr} WHERE ".$this->buildWhereLikeChain($col);
                $affected = DB::update($sql);
                $this->comment("Atualizadas: {$affected}");
            } else {
                $this->comment('Nenhuma alteração aplicada (dry-run). Use --apply para gravar.');
            }
        }

        $this->line('');
        $this->comment('Concluído.');
        return self::SUCCESS;
    }

    /** Query base para linhas com mojibake na coluna */
    protected function suspectQuery(string $table, string $col, ?int $limit = null)
    {
        $q = DB::table($table)->select('*');
        $q->where(function($w) use ($col) {
            foreach (array_keys($this->map) as $needle) {
                $w->orWhere($col, 'like', '%'.$needle.'%');
            }
        });
        if ($limit) $q->limit($limit);
        return $q;
    }

    /** Corrige string em PHP (usado para prévia) */
    protected function fix(string $s): string
    {
        if ($s === '') return $s;
        $s2 = strtr($s, $this->map);
        // limpeza adicional de espaços estranhos
        $s2 = preg_replace('/\s+/u', ' ', $s2);
        return trim($s2);
    }

    /** Monta cadeia REPLACE(REPLACE(col,'Ã¡','á'),...) para UPDATE via SQL */
    protected function buildSqlReplaceChain(string $col): string
    {
        $expr = $col;
        foreach ($this->map as $bad => $good) {
            // escapa aspas simples
            $bad  = str_replace("'", "\\'", $bad);
            $good = str_replace("'", "\\'", $good);
            $expr = "REPLACE({$expr},'{$bad}','{$good}')";
        }
        return $expr;
    }

    /** Monta WHERE col LIKE '%bad1%' OR col LIKE '%bad2%' ...  */
    protected function buildWhereLikeChain(string $col): string
    {
        $parts = [];
        foreach (array_keys($this->map) as $bad) {
            $bad = str_replace("'", "\\'", $bad);
            $parts[] = "{$col} LIKE '%{$bad}%'";
        }
        return '('.implode(' OR ', $parts).')';
    }
}
