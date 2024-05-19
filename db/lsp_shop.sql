CREATE DATABASE lsp_shop DEFAULT CHARSET=utf8;

CREATE TABLE platform
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY(id),
  CONSTRAINT UK_PLATFORM_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO platform(id, name) VALUES (1, 'any');
INSERT INTO platform(id, name) VALUES (2, 'linux');
INSERT INTO platform(id, name) VALUES (3, 'freebsd');
INSERT INTO platform(id, name) VALUES (4, 'windows');
INSERT INTO platform(id, name) VALUES (5, 'macos');
INSERT INTO platform(id, name) VALUES (6, 'haiku');

CREATE TABLE architecture
(
  id int NOT NULL,
  name VARCHAR(32) NOT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_ARCHITECTURE_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO architecture(id, name) VALUES (1, 'noarch');
INSERT INTO architecture(id, name) VALUES (2, 'i586');
INSERT INTO architecture(id, name) VALUES (3, 'x86_64');
INSERT INTO architecture(id, name) VALUES (4, 'armv7a');
INSERT INTO architecture(id, name) VALUES (5, 'aarch64');
INSERT INTO architecture(id, name) VALUES (6, 'riscv64');

CREATE TABLE build_type
(
  id int NOT NULL,
  name VARCHAR(16) NOT NULL,
  
  PRIMARY KEY (id),
  CONSTRAINT UK_BUILD_TYPE_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO build_type(id, name) VALUES (1, 'release');
INSERT INTO build_type(id, name) VALUES (2, 'update');
INSERT INTO build_type(id, name) VALUES (3, 'enhancement');

CREATE TABLE format
(
  id int NOT NULL,
  name VARCHAR(16) NOT NULL,
  PRIMARY KEY (id),
  CONSTRAINT UK_FORMAT_ID UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO format(id, name) VALUES (1, 'src');
INSERT INTO format(id, name) VALUES (2, 'doc');
INSERT INTO format(id, name) VALUES (3, 'jack');
INSERT INTO format(id, name) VALUES (4, 'pw');
INSERT INTO format(id, name) VALUES (5, 'ladspa');
INSERT INTO format(id, name) VALUES (6, 'lv2');
INSERT INTO format(id, name) VALUES (7, 'vst2');
INSERT INTO format(id, name) VALUES (8, 'vst3');
INSERT INTO format(id, name) VALUES (9, 'clap');
INSERT INTO format(id, name) VALUES (10, 'gst');
INSERT INTO format(id, name) VALUES (11, 'au');
INSERT INTO format(id, name) VALUES (12, 'aax');
INSERT INTO format(id, name) VALUES (13, 'rtas');

CREATE TABLE product
(
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,

  PRIMARY KEY (id),
  CONSTRAINT UK_PRODUCT_NAME UNIQUE KEY (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO product(name) VALUES ('lsp-plugins');

CREATE TABLE build
(
  id bigint(20) NOT NULL auto_increment,
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

CREATE TABLE artifact
(
  id bigint(20) NOT NULL auto_increment,
  build_id bigint(20) NOT NULL,
  platform_id int NOT NULL,
  architecture_id int NOT NULL,
  format_id int NOT NULL,
  file_name VARCHAR(1024) NOT NULL,
  
  PRIMARY KEY PK_ARTIFACT_ID (id), 
  CONSTRAINT UK_ARTIFACT_FILE UNIQUE KEY (file_name),
  CONSTRAINT UK_ARTIFACT_LINK UNIQUE KEY (build_id, platform_id, architecture_id, format_id),
  CONSTRAINT FK_ARTIFACT_BUILD FOREIGN KEY (build_id) REFERENCES build(id),
  CONSTRAINT FK_ARTIFACT_PLAT FOREIGN KEY (platform_id) REFERENCES platform(id),
  CONSTRAINT FK_ARTIFACT_ARCH FOREIGN KEY (architecture_id) REFERENCES architecture(id),
  CONSTRAINT FK_ARTIFACT_FMT FOREIGN KEY (format_id) REFERENCES format(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE price
(
  build_id bigint(20) NOT NULL,
  platform_id int NOT NULL,
  
  initial_price int,
  update_price int,
  
  PRIMARY KEY (build_id, platform_id),
  CONSTRAINT FK_PRICE_BLD FOREIGN KEY (build_id) REFERENCES build(id),
  CONSTRAINT FK_PRICE_PLAT FOREIGN KEY (platform_id) REFERENCES platform(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



