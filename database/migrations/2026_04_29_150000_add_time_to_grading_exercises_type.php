<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite CHECK constraints can't be altered — recreate the table with updated type enum
        DB::statement('CREATE TABLE grading_exercises_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            class_type_id INTEGER NOT NULL,
            type VARCHAR CHECK(type IN (\'marks\',\'rating\',\'time\')) NOT NULL,
            name VARCHAR NOT NULL,
            description TEXT,
            starting_marks NUMERIC,
            target_time_seconds INTEGER UNSIGNED,
            allow_second_attempt TINYINT(1) NOT NULL DEFAULT 0,
            sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (class_type_id) REFERENCES class_types(id) ON DELETE CASCADE
        )');

        DB::statement('INSERT INTO grading_exercises_new SELECT * FROM grading_exercises');
        DB::statement('DROP TABLE grading_exercises');
        DB::statement('ALTER TABLE grading_exercises_new RENAME TO grading_exercises');
    }

    public function down(): void
    {
        DB::statement('CREATE TABLE grading_exercises_new (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
            class_type_id INTEGER NOT NULL,
            type VARCHAR CHECK(type IN (\'marks\',\'rating\')) NOT NULL,
            name VARCHAR NOT NULL,
            description TEXT,
            starting_marks NUMERIC,
            target_time_seconds INTEGER UNSIGNED,
            allow_second_attempt TINYINT(1) NOT NULL DEFAULT 0,
            sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
            created_at DATETIME,
            updated_at DATETIME,
            FOREIGN KEY (class_type_id) REFERENCES class_types(id) ON DELETE CASCADE
        )');

        DB::statement('INSERT INTO grading_exercises_new SELECT * FROM grading_exercises WHERE type IN (\'marks\', \'rating\')');
        DB::statement('DROP TABLE grading_exercises');
        DB::statement('ALTER TABLE grading_exercises_new RENAME TO grading_exercises');
    }
};
