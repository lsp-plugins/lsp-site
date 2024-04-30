CREATE DATABASE lsp_clients DEFAULT CHARSET=utf8;

CREATE TABLE order_status
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY(id),
  CONSTRAINT UK_ORDER_STATUS_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO order_status (id, name) VALUES (1, 'created');
INSERT INTO order_status (id, name) VALUES (2, 'paid');
INSERT INTO order_status (id, name) VALUES (3, 'verified');
INSERT INTO order_status (id, name) VALUES (4, 'cancelled');
INSERT INTO order_status (id, name) VALUES (5, 'refunded');

CREATE TABLE customer_type
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY(id),
  CONSTRAINT UK_CUSTOMER_TYPE_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO customer_type (id, name) VALUES (1, 'regular');
INSERT INTO customer_type (id, name) VALUES (2, 'developer');
INSERT INTO customer_type (id, name) VALUES (3, 'reviewer');
INSERT INTO customer_type (id, name) VALUES (4, 'tester');

CREATE TABLE customer
(
  id bigint(20) NOT NULL auto_increment,
  support_id varchar(36) NOT NULL,
  email varchar(1024) NOT NULL,
  password varchar(64) NOT NULL,
  type int NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT current_timestamp,
  verified TIMESTAMP NULL DEFAULT NULL,
  blocked TIMESTAMP NULL DEFAULT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_CUSTOMER_EMAIL UNIQUE KEY(email),
  CONSTRAINT FK_CUSTOMER_TYPE FOREIGN KEY(type) REFERENCES customer_type(id),
  CONSTRAINT UK_CUSTOMER_SUPPORT_ID UNIQUE KEY(support_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE customer_token
(
  id varchar(36) NOT NULL,
  customer_id bigint(20) NOT NULL,
  created TIMESTAMP NOT NULL DEFAULT current_timestamp,
  expire TIMESTAMP NULL DEFAULT current_timestamp,
  scope VARCHAR(32) NOT NULL,
  data TEXT NULL,
  
  CONSTRAINT PK_CUSTOMER_TOKEN_ID PRIMARY KEY(id),
  CONSTRAINT FK_CUSTOMER_TOKEN_CID FOREIGN KEY(customer_id) REFERENCES customer(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE orders
(
  id bigint(20) NOT NULL,
  submit_time TIMESTAMP NOT NULL,
  customer_id bigint(20) NOT NULL,
  build_id int NOT NULL,
  refund_time TIMESTAMP,
  complete_time TIMESTAMP,
  status int NOT NULL,
  amount int NOT NULL,
    
  PRIMARY KEY (id),
  CONSTRAINT FK_ORDER_CUST FOREIGN KEY (customer_id) REFERENCES customer(id),
  CONSTRAINT UK_ORDER_STATUS FOREIGN KEY (status) REFERENCES order_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX IDX_ORDERS_TIME ON orders(submit_time);

CREATE TABLE purchase
(
  id bigint(20) NOT NULL,
  customer_id bigint(20) NOT NULL,
  build_id int NOT NULL,
  purchase_date TIMESTAMP NOT NULL,
  order_id bigint(20) NOT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_PURCHASE_BLD UNIQUE KEY (customer_id, build_id),
  CONSTRAINT FK_PURCHASE_CUST FOREIGN KEY (customer_id) REFERENCES customer(id),
  CONSTRAINT FK_PURCHASE_ORD FOREIGN KEY (order_id) REFERENCES orders(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE customer_log
(
  time TIMESTAMP NOT NULL,
  customer_id bigint(20) NOT NULL,
  session_id varchar(36) NOT NULL,
  action varchar(64) NOT NULL,
  data text NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX IDX_CUSTOMER_LOG_CID ON customer_log(customer_id);
CREATE INDEX IDX_CUSTOMER_LOG_SID ON customer_log(session_id);

