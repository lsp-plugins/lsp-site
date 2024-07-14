ALTER TABLE sessions ADD private_id VARCHAR(36);
UPDATE sessions set private_id=uuid();

