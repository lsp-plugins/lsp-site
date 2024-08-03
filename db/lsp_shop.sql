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
INSERT INTO build_type(id, name) VALUES (4, 'major');

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
INSERT INTO format(id, name) VALUES (14, 'multi');

CREATE TABLE product
(
  id int NOT NULL AUTO_INCREMENT,
  name VARCHAR(64) NOT NULL,
  description VARCHAR(128),
  price BIGINT(20),

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
  price BIGINT(20),
  version_raw int NOT NULL,
  
  PRIMARY KEY (id),
  UNIQUE KEY (product_id, major, minor, micro),
  CONSTRAINT FK_BUILD_PID FOREIGN KEY (product_id) REFERENCES product(id),
  CONSTRAINT FK_BUILD_TYPE FOREIGN KEY (type_id) REFERENCES build_type(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE artifact
(
  id varchar(36) NOT NULL,
  build_id bigint(20) NOT NULL,
  platform_id int NOT NULL,
  architecture_id int NOT NULL,
  format_id int NOT NULL,
  file_name VARCHAR(1024) NOT NULL,
  
  CONSTRAINT PK_ARTIFACT_ID PRIMARY KEY(id), 
  CONSTRAINT UK_ARTIFACT_FILE UNIQUE KEY (file_name),
  CONSTRAINT UK_ARTIFACT_LINK UNIQUE KEY (build_id, platform_id, architecture_id, format_id),
  CONSTRAINT FK_ARTIFACT_BUILD FOREIGN KEY (build_id) REFERENCES build(id),
  CONSTRAINT FK_ARTIFACT_PLAT FOREIGN KEY (platform_id) REFERENCES platform(id),
  CONSTRAINT FK_ARTIFACT_ARCH FOREIGN KEY (architecture_id) REFERENCES architecture(id),
  CONSTRAINT FK_ARTIFACT_FMT FOREIGN KEY (format_id) REFERENCES format(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP VIEW IF EXISTS v_artifacts;
CREATE VIEW v_artifacts
AS
  SELECT
    p.id product_id, p.name product, p.description description,
    a.build_id build_id, b.type_id type_id, bt.name type,
    a.id artifact_id,
    b.major version_major, b.minor version_minor, b.micro version_micro, b.version_raw version_raw,
    a.platform_id platform_id, pl.name platform,
    a.architecture_id architecture_id, arch.name architecture,
    a.format_id format_id, fmt.name format,
    a.file_name file_name
  FROM artifact a
  INNER JOIN build b
  ON (b.id = a.build_id)
  INNER JOIN product p
  ON (p.id = b.product_id)
  INNER JOIN format fmt
  ON (fmt.id = a.format_id)
  INNER JOIN architecture arch
  ON (arch.id = a.architecture_id)
  INNER JOIN platform pl
  ON (pl.id = a.platform_id)
  INNER JOIN build_type bt
  ON (bt.id = b.type_id);

DROP VIEW IF EXISTS v_latest_artifacts;
CREATE VIEW v_latest_artifacts
AS
  SELECT
    p.id product_id, p.name product, p.description description,
    a.build_id build_id, b.type_id type_id, bt.name type,
    a.id artifact_id,
    b.major version_major, b.minor version_minor, b.micro version_micro, b.version_raw version_raw,
    a.platform_id platform_id, pl.name platform,
    a.architecture_id architecture_id, arch.name architecture,
    a.format_id format_id, fmt.name format,
    a.file_name file_name
  FROM (
    SELECT
      build.product_id, artifact.platform_id, max(version_raw) version_raw
    FROM build
    INNER JOIN artifact
    ON (artifact.build_id = build.id)
    GROUP BY build.product_id, artifact.platform_id
  ) bb
  INNER JOIN build b
  ON (b.product_id = bb.product_id) AND (b.version_raw = bb.version_raw)
  INNER JOIN product p
  ON (p.id = b.product_id)
  INNER JOIN artifact a
  ON (a.build_id = b.id) AND (a.platform_id = bb.platform_id)
  INNER JOIN format fmt
  ON (fmt.id = a.format_id)
  INNER JOIN architecture arch
  ON (arch.id = a.architecture_id)
  INNER JOIN platform pl
  ON (pl.id = a.platform_id)
  INNER JOIN build_type bt
  ON (bt.id = b.type_id);


