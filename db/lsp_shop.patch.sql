ALTER TABLE artifact DROP column id;
ALTER TABLE artifact ADD COLUMN id varchar(36);
UPDATE artifact set id=uuid();
ALTER TABLE artifact MODIFY id varchar(36) NOT NULL FIRST;
ALTER TABLE artifact ADD CONSTRAINT PK_ARTIFACT_ID PRIMARY KEY(id);

-- ALTER TABLE artifact ADD COLUMN major int;
-- ALTER TABLE artifact ADD COLUMN minor int;
-- ALTER TABLE artifact ADD COLUMN micro int;
-- ALTER TABLE artifact ADD COLUMN version_raw int;

-- UPDATE artifact a
-- SET major = ( SELECT b.major FROM build b WHERE (b.id = a.build_id) ),
--   minor = ( SELECT b.minor FROM build b WHERE (b.id = a.build_id) ),
--   micro = ( SELECT b.micro FROM build b WHERE (b.id = a.build_id) );

ALTER TABLE build ADD COLUMN version_raw int;
UPDATE build SET version_raw=(major * 1000 + minor) * 1000 + micro;
ALTER TABLE build MODIFY version_raw int NOT NULL;

DROP VIEW IF EXISTS v_artifacts;
CREATE VIEW v_artifacts
AS
  SELECT
    p.id product_id, p.name product,
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
    p.id product_id, p.name product,
    a.build_id build_id, b.type_id type_id, bt.name type,
    a.id artifact_id,
    b.major version_major, b.minor version_minor, b.micro version_micro, b.version_raw version_raw,
    a.platform_id platform_id, pl.name platform,
    a.architecture_id architecture_id, arch.name architecture,
    a.format_id format_id, fmt.name format,
    a.file_name file_name
  FROM (
    SELECT
      product_id, max(version_raw) version_raw
    FROM build
  ) bb
  INNER JOIN build b
  ON (b.product_id = bb.product_id) AND (b.version_raw = bb.version_raw)
  INNER JOIN product p
  ON (p.id = b.product_id)
  INNER JOIN artifact a
  ON (a.build_id = b.id)
  INNER JOIN format fmt
  ON (fmt.id = a.format_id)
  INNER JOIN architecture arch
  ON (arch.id = a.architecture_id)
  INNER JOIN platform pl
  ON (pl.id = a.platform_id)
  INNER JOIN build_type bt
  ON (bt.id = b.type_id);
    
    
    
    