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

ALTER TABLE product ADD COLUMN description VARCHAR(128);
ALTER TABLE product ADD COLUMN price BIGINT(20);
ALTER TABLE build ADD COLUMN price BIGINT(20);

UPDATE product set description='LSP Plugins Full Bundle' where name='lsp-plugins';
UPDATE product set description='A/B Tester' where name='lsp-plugins-ab-tester';
UPDATE product set description='Artistic Delay' where name='lsp-plugins-art-delay';
UPDATE product set description='Beat Breather' where name='lsp-plugins-beat-breather';
UPDATE product set description='Chorus' where name='lsp-plugins-chorus';
UPDATE product set description='Clipper' where name='lsp-plugins-clipper';
UPDATE product set description='Compensation Delay' where name='lsp-plugins-comp-delay';
UPDATE product set description='Filter' where name='lsp-plugins-filter';
UPDATE product set description='Flanger' where name='lsp-plugins-flanger';
UPDATE product set description='Impulse Responses' where name='lsp-plugins-impulse-responses';
UPDATE product set description='Impulse Reverb' where name='lsp-plugins-impulse-reverb';
UPDATE product set description='Latency Meter' where name='lsp-plugins-latency-meter';
UPDATE product set description='Loudness Compensator' where name='lsp-plugins-loud-comp';
UPDATE product set description='Multiband Clipper' where name='lsp-plugins-mb-clipper';
UPDATE product set description='Mixer' where name='lsp-plugins-mixer';
UPDATE product set description='Noise Generator' where name='lsp-plugins-noise-generator';
UPDATE product set description='Oscillator' where name='lsp-plugins-oscillator';
UPDATE product set description='Oscilloscope' where name='lsp-plugins-oscilloscope';
UPDATE product set description='Phase Detector' where name='lsp-plugins-phase-detector';
UPDATE product set description='Profiler' where name='lsp-plugins-profiler';
UPDATE product set description='Slap-back Delay' where name='lsp-plugins-slap-delay';
UPDATE product set description='Spectrum Analyzer' where name='lsp-plugins-spectrum-analyzer';
UPDATE product set description='Surge Filter' where name='lsp-plugins-surge-filter';

ALTER TABLE build ADD COLUMN version_raw int;
UPDATE build SET version_raw=(major * 1000 + minor) * 1000 + micro;
ALTER TABLE build MODIFY version_raw int NOT NULL;

DROP TABLE price;

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

    
    