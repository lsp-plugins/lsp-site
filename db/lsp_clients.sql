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
  CONSTRAINT FK_CUSTOMER_TOKEN_CID FOREIGN KEY(customer_id) REFERENCES customer(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE orders;

CREATE TABLE orders
(
  id varchar(36) NOT NULL,
  remote_id varchar(128),
  customer_id bigint(20) NOT NULL,
  product_id int NOT NULL,
  version_raw int NOT NULL,
  submit_time TIMESTAMP NOT NULL,
  refund_time TIMESTAMP,
  complete_time TIMESTAMP,
  status int NOT NULL,
  amount bigint(20) NOT NULL,

  PRIMARY KEY (id),
  CONSTRAINT FK_ORDER_CUST FOREIGN KEY (customer_id) REFERENCES customer(id),
  CONSTRAINT UK_ORDER_STATUS FOREIGN KEY (status) REFERENCES order_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE INDEX IDX_ORDERS_TIME ON orders(submit_time);

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

CREATE VIEW v_latest_orders
AS
  SELECT
  	o.customer_id customer_id,
  	o.product_id product_id,
  	max(o.version_raw) version_raw
  FROM orders o
  INNER JOIN order_status os
  ON (os.id = o.status)
  WHERE
    os.name in ('paid', 'verified')
  GROUP BY
    o.customer_id,
    o.product_id;

