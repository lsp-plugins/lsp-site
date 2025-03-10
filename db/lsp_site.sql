CREATE TABLE sessions (
  id VARCHAR(36) NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT current_timestamp,
  expire TIMESTAMP NOT NULL DEFAULT current_timestamp,
  user_id bigint(20) DEFAULT NULL,
  context TEXT,
  
  CONSTRAINT PK_SESSION_ID UNIQUE(id) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX ON sessions(user_id);
CREATE INDEX IDX_SESSION_USED ON sessions(used);

CREATE TABLE csrf_tokens (
  id VARCHAR(36) NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT current_timestamp,
  expire TIMESTAMP NULL DEFAULT current_timestamp,
  session_id VARCHAR(36) NOT NULL,
  scope VARCHAR(32) NOT NULL,
  
  CONSTRAINT PK_CSRF_TOKEN_ID PRIMARY KEY (id),
  CONSTRAINT FK_CSRF_TOKEN_SID FOREIGN KEY (session_id) REFERENCES sessions(id) ON DELETE CASCADE  
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

