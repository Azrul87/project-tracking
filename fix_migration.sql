-- Clean up partial migration state
-- Run these SQL commands in phpMyAdmin

-- 1. Check current state
SHOW TABLES LIKE 'project_materials%';

-- 2. Drop the new project_materials table (from partial migration)
DROP TABLE IF EXISTS `project_materials`;

-- 3. Check if materials table exists
SHOW TABLES LIKE 'materials';

-- If materials table exists, drop it too:
DROP TABLE IF EXISTS `materials`;

-- 4. Now check what migrations have run
SELECT * FROM migrations WHERE migration LIKE '%materials%';

-- 5. Delete the failed migration records
DELETE FROM migrations WHERE migration LIKE '2026_01_11_15000%';

-- 6. Now re-run the migration from command line:
-- php artisan migrate
