USE car_dealership;

-- Add remember_token column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS remember_token VARCHAR(64) DEFAULT NULL; 