-- Create DB if not exists (your request)
CREATE DATABASE IF NOT EXISTS `inv_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

-- Optional: if your .env DB_DATABASE is inv_db, you're done.
-- If you keep a different DB_DATABASE, this still ensures inv_db exists.
