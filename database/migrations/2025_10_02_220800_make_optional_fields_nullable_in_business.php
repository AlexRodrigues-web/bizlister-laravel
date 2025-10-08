<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    private string $table = "business";

    // Colunas candidatas a ficarem opcionais (será ignorada se não existir)
    private array $cols = [
        "address_1","address_2","postcode","phone","mobile","email",
        "website","facebook","twitter","instagram","lat","lng","hours",
        "image_path","image"
    ];

    private function columnType(string $col): ?string
    {
        $dbName = config("database.connections.mysql.database");
        $row = DB::selectOne("
            SELECT COLUMN_TYPE
            FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = ?
            LIMIT 1
        ", [$dbName, $this->table, $col]);

        return $row?->COLUMN_TYPE ?? null;
    }

    private function setNullable(string $col, bool $nullable): void
    {
        $colType = $this->columnType($col);
        if (!$colType) return;

        // Monta o MODIFY preservando o tipo exato
        $nullSql = $nullable ? "NULL DEFAULT NULL" : "NOT NULL";
        $sql = "ALTER TABLE `{$this->table}` MODIFY `{$col}` {$colType} {$nullSql}";
        DB::statement($sql);
    }

    public function up(): void
    {
        if (!Schema::hasTable($this->table)) return;

        foreach ($this->cols as $c) {
            if (Schema::hasColumn($this->table, $c)) {
                $this->setNullable($c, true);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable($this->table)) return;

        foreach ($this->cols as $c) {
            if (Schema::hasColumn($this->table, $c)) {
                $this->setNullable($c, false);
            }
        }
    }
};