<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use a transaction so either all changes are applied or none.
        DB::transaction(function () {
            // Drop the old global unique index on name if it exists (different installs may have different names)
            try {
                DB::statement('DROP INDEX IF EXISTS categories_name_unique');
            } catch (\Throwable $e) {
                // ignore
            }
            try {
                DB::statement('DROP INDEX IF EXISTS name_unique');
            } catch (\Throwable $e) {
                // ignore
            }

            // Deduplicate categories per user_id + name while preserving the lowest id
            // and reassign any transactions referencing duplicate categories to the kept id.
            // This uses a CTE with window functions (Postgres) to find duplicates.
            // If the DB does not support window functions this will throw and abort the migration.
                        // Update transactions to point to the kept category id (per user_id + name)
                        DB::statement(<<<'SQL'
WITH ranked AS (
    SELECT id, user_id, name, MIN(id) OVER (PARTITION BY user_id, name) AS keep_id
    FROM categories
), dupes AS (
    SELECT id, keep_id FROM ranked WHERE id <> keep_id
)
UPDATE transactions
SET category_id = dupes.keep_id
FROM dupes
WHERE transactions.category_id = dupes.id;
SQL
                        );

                        // Delete duplicate category rows (those whose id is not the kept id)
                        DB::statement(<<<'SQL'
WITH ranked AS (
    SELECT id, user_id, name, MIN(id) OVER (PARTITION BY user_id, name) AS keep_id
    FROM categories
)
DELETE FROM categories c
USING ranked r
WHERE c.id = r.id AND r.id <> r.keep_id;
SQL
                        );

            // Finally, create the composite unique index on (user_id, name)
            Schema::table('categories', function (Blueprint $table) {
                $table->unique(['user_id', 'name'], 'categories_user_id_name_unique');
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::transaction(function () {
            // Drop composite unique index
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('categories_user_id_name_unique');
            });

            // Recreate the old global unique index on name. Note: this will fail if duplicate
            // names across users exist; we leave this as the historical reverse but it's
            // likely unsafe for shared installs. Use with caution.
            Schema::table('categories', function (Blueprint $table) {
                $table->unique('name');
            });
        });
    }
};
