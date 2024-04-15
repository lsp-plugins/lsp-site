CREATE DATABASE lsp_shop DEFAULT CHARSET=utf8;

CREATE TABLE platform
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY(id),
  CONSTRAINT UK_PLATFORM_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO platform(id, name) VALUES (1, 'linux');
INSERT INTO platform(id, name) VALUES (2, 'freebsd');
INSERT INTO platform(id, name) VALUES (3, 'windows');
INSERT INTO platform(id, name) VALUES (4, 'macos');

CREATE TABLE architecture
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_ARCHITECTURE_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO architecture(id, name) VALUES (1, 'i586');
INSERT INTO architecture(id, name) VALUES (2, 'x86_64');
INSERT INTO architecture(id, name) VALUES (3, 'armv7a');
INSERT INTO architecture(id, name) VALUES (4, 'aarch64');
INSERT INTO architecture(id, name) VALUES (5, 'riscv64');

CREATE TABLE build_type
(
  id int NOT NULL,
  name VARCHAR(16) NOT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_BUILD_TYPE_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO build_type(id, name) VALUES (1, 'minor_update');
INSERT INTO build_type(id, name) VALUES (2, 'new_features');
INSERT INTO build_type(id, name) VALUES (3, 'major_update');

CREATE TABLE product
(
  id int NOT NULL AUTO_INCREMENT,
  product_id VARCHAR(64) NOT NULL,

  PRIMARY KEY (id),
  CONSTRAINT UK_PRODUCT_ID UNIQUE KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE build
(
  id int NOT NULL,
  product_id int NOT NULL,
  issue_date DATE NOT NULL,
  
  type_id int NOT NULL,
  major int NOT NULL,
  minor int NOT NULL,
  micro int NOT NULL,
  
  PRIMARY KEY (id),
  UNIQUE KEY (product_id, major, minor, micro),
  CONSTRAINT FK_BUILD_PID FOREIGN KEY (product_id) REFERENCES product(id),
  CONSTRAINT FK_BUILD_TYPE FOREIGN KEY (type_id) REFERENCES build_type(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE package
(
  build_id int NOT NULL,
  platform_id int NOT NULL,
  architecture_id int NOT NULL,
  file_name VARCHAR(1024) NOT NULL,
  
  PRIMARY KEY (build_id, architecture_id),
  CONSTRAINT UK_PACKAGE_FILE UNIQUE KEY (file_name),
  CONSTRAINT FK_PACKAGE_PLAT FOREIGN KEY (platform_id) REFERENCES platform(id),
  CONSTRAINT FK_PACKAGE_BUILD FOREIGN KEY (build_id) REFERENCES build(id),
  CONSTRAINT FK_PACKAGE_ARCH FOREIGN KEY (architecture_id) REFERENCES architecture(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE price
(
  build_id int NOT NULL,
  platform_id int NOT NULL,
  
  initial_price int,
  update_price int,
  
  PRIMARY KEY (build_id, platform_id),
  CONSTRAINT FK_PRICE_BLD FOREIGN KEY (build_id) REFERENCES build(id),
  CONSTRAINT FK_PRICE_PLAT FOREIGN KEY (platform_id) REFERENCES platform(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


