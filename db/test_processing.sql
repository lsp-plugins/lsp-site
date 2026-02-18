CREATE DATABASE test_processing DEFAULT CHARSET=utf8;

CREATE TABLE order_status
(
	id INT NOT NULL,
	name VARCHAR(16) NOT NULL,
	
	CONSTRAINT PK_ORDER_STATUS PRIMARY KEY(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO order_status(id, name) value (1, 'active');
INSERT INTO order_status(id, name) value (2, 'success');
INSERT INTO order_status(id, name) value (3, 'cancel');
INSERT INTO order_status(id, name) value (4, 'timeout');

CREATE TABLE orders
(
  id varchar(36) NOT NULL,
  amount BIGINT NOT NULL,
  status_id int NOT NULL,
  success_url VARCHAR(255),
  cancel_url VARCHAR(255),
  client_data TINYTEXT,
  created TIMESTAMP NOT NULL,
  expires TIMESTAMP NOT NULL, 
  
  PRIMARY KEY(id),
  CONSTRAINT FK_ORDER_STATUS FOREIGN KEY (status_id) REFERENCES order_status(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
