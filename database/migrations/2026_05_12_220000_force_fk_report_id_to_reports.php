<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop semua foreign key yang terkait kolom report_id di tabel_status (aman karena constraint name bisa beda)
        $constraints = DB::select("
            SELECT
                tc.CONSTRAINT_NAME
            FROM
                information_schema.KEY_COLUMN_USAGE kcu
                INNER JOIN information_schema.TABLE_CONSTRAINTS tc
                    ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                    AND tc.TABLE_SCHEMA = kcu.TABLE_SCHEMA
            WHERE
                kcu.TABLE_SCHEMA = DATABASE()
                AND kcu.TABLE_NAME = 'tabel_status'
                AND kcu.COLUMN_NAME = 'report_id'
                AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        foreach ($constraints as $c) {
            $name = $c->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE tabel_status DROP FOREIGN KEY {$name}");
        }

        // Pastikan foreign key report_id mengarah ke reports(id)
        // (NOT NULL/NULL tidak diatur di sini; hanya constraint foreign key)
        DB::statement("
            ALTER TABLE tabel_status
            ADD CONSTRAINT tabel_status_report_id_foreign
            FOREIGN KEY (report_id)
            REFERENCES reports(id)
            ON DELETE CASCADE
        ");
    }

    public function down(): void
    {
        // Saat rollback, kita kembalikan ke constraint yang benar sesuai definisi aplikasi (reports)
        // (Rollback untuk arah tabel_laporan tidak lagi relevan dengan relasi model saat ini)
        $constraints = DB::select("
            SELECT
                tc.CONSTRAINT_NAME
            FROM
                information_schema.KEY_COLUMN_USAGE kcu
                INNER JOIN information_schema.TABLE_CONSTRAINTS tc
                    ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME
                    AND tc.TABLE_SCHEMA = kcu.TABLE_SCHEMA
            WHERE
                kcu.TABLE_SCHEMA = DATABASE()
                AND kcu.TABLE_NAME = 'tabel_status'
                AND kcu.COLUMN_NAME = 'report_id'
                AND tc.CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        foreach ($constraints as $c) {
            $name = $c->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE tabel_status DROP FOREIGN KEY {$name}");
        }

        DB::statement("
            ALTER TABLE tabel_status
            ADD CONSTRAINT tabel_status_report_id_foreign
            FOREIGN KEY (report_id)
            REFERENCES reports(id)
            ON DELETE CASCADE
        ");
    }
};
