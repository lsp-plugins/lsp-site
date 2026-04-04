alter table product add short_desc varchar(64);
UPDATE product set short_desc = description;

DROP VIEW IF EXISTS v_artifacts;
CREATE VIEW v_artifacts
AS
  SELECT
    p.id product_id, p.name product, p.bundle_name bundle, p.description description, p.short_desc short_desc,
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
    p.id product_id, p.name product, p.bundle_name bundle, p.description description, p.short_desc short_desc,
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




UPDATE product set description='A/B Tester', short_desc='A/B Tester', bundle_name='ab_tester', price=15*100000 where name='lsp-plugins-ab-tester';
UPDATE product set description='Artistic Delay', short_desc='Artistic Delay', bundle_name='art_delay', price=15*100000 where name='lsp-plugins-art-delay';
UPDATE product set description='Beat Breather', short_desc='Beat Breather', bundle_name='beat_breather', price=45*100000 where name='lsp-plugins-beat-breather';
UPDATE product set description='Chorus', short_desc='Chorus', bundle_name='chorus', price=20*100000 where name='lsp-plugins-chorus';
UPDATE product set description='Clipper', short_desc='Clipper', bundle_name='clipper' where name='lsp-plugins-clipper';
UPDATE product set description='Compensation Delay', short_desc='Compensation Delay', bundle_name='comp_delay', price=10*100000 where name='lsp-plugins-comp-delay';
UPDATE product set description='Filter', short_desc='Filter', bundle_name='filter', price=20*100000 where name='lsp-plugins-filter';
UPDATE product set description='Flanger', short_desc='Flanger', bundle_name='flanger', price=20*100000 where name='lsp-plugins-flanger';
UPDATE product set description='Impulse Responses', short_desc='Impulse Responses', bundle_name='impulse_responses', price=25*100000 where name='lsp-plugins-impulse-responses';
UPDATE product set description='Impulse Reverb', short_desc='Impulse Reverb', bundle_name='impulse_reverb', price=35*100000 where name='lsp-plugins-impulse-reverb';
UPDATE product set description='Latency Meter', short_desc='Latency Meter', bundle_name='latency_meter', price=15*100000 where name='lsp-plugins-latency-meter';
UPDATE product set description='Loudness Compensator', short_desc='Loudness Compensator', bundle_name='loud_comp', price=20*100000 where name='lsp-plugins-loud-comp';
UPDATE product set description='Multiband Clipper', short_desc='M/B Clipper', bundle_name='mb_clipper', price=40*100000 where name='lsp-plugins-mb-clipper';
UPDATE product set description='Mixer', short_desc='Mixer', bundle_name='mixer', price=25*100000 where name='lsp-plugins-mixer';
UPDATE product set description='Noise Generator', short_desc='Noise Generator', bundle_name='noise_generator', price=10*100000 where name='lsp-plugins-noise-generator';
UPDATE product set description='Oscillator', short_desc='Oscillator', bundle_name='oscillator', price=0*100000 where name='lsp-plugins-oscillator';
UPDATE product set description='Oscilloscope', short_desc='Oscilloscope', bundle_name='oscilloscope', price=30*100000 where name='lsp-plugins-oscilloscope';
UPDATE product set description='Phase Detector', short_desc='Phase Detector', bundle_name='phase_detector', price=20*100000 where name='lsp-plugins-phase-detector';
UPDATE product set description='Profiler', short_desc='Profiler', bundle_name='profiler', price=20*100000 where name='lsp-plugins-profiler';
UPDATE product set description='Slap-back Delay', short_desc='Slap-back Delay', bundle_name='slap_delay', price=15*100000 where name='lsp-plugins-slap-delay';
UPDATE product set description='Spectrum Analyzer', short_desc='Spectrum Analyzer', bundle_name='spectrum_analyzer', price=30*100000 where name='lsp-plugins-spectrum-analyzer';
UPDATE product set description='Surge Filter', short_desc='Surge Filter', bundle_name='surge_filter', price=15*100000 where name='lsp-plugins-surge-filter';
UPDATE product set description='Automatic Gain Control', short_desc='Automatic Gain Control', bundle_name='autogain', price=20*100000 where name='lsp-plugins-autogain';
UPDATE product set description='Compressor', short_desc='Compressor', bundle_name='compressor', price=40*100000 where name='lsp-plugins-compressor';
UPDATE product set description='Crossover', short_desc='Crossover', bundle_name='crossover', price=20*100000 where name='lsp-plugins-crossover';
UPDATE product set description='Dynamics Processor', short_desc='Dynamics Processor', bundle_name='dyna_processor', price=40*100000 where name='lsp-plugins-dyna-processor';
UPDATE product set description='Expander', short_desc='Expander', bundle_name='expander', price=40*100000 where name='lsp-plugins-expander';
UPDATE product set description='Gate', short_desc='Gate', bundle_name='gate', price=40*100000 where name='lsp-plugins-gate';
UPDATE product set description='GOTT Compressor', short_desc='GOTT Compressor', bundle_name='gott_compressor', price=40*100000 where name='lsp-plugins-gott-compressor';
UPDATE product set description='Graphic Equalizer', short_desc='Graphic Equalizer', bundle_name='graph_equalizer', price=40*100000 where name='lsp-plugins-graph-equalizer';
UPDATE product set description='Limiter', short_desc='Limiter', bundle_name='limiter', price=30*100000 where name='lsp-plugins-limiter';
UPDATE product set description='Matcher', short_desc='Matcher', bundle_name='matcher', price=30*100000 where name='lsp-plugins-matcher';
UPDATE product set description='Multiband Compressor', short_desc='M/B Compressor', bundle_name='mb_compressor', price=40*100000 where name='lsp-plugins-mb-compressor';
UPDATE product set description='Multiband Dynamics Processor', short_desc='M/B Dynamics Processor', bundle_name='mb_dyna_processor', price=40*100000 where name='lsp-plugins-mb-dyna-processor';
UPDATE product set description='Multiband Expander', short_desc='M/B Expander', bundle_name='mb_expander', price=40*100000 where name='lsp-plugins-mb-expander';
UPDATE product set description='Multiband Gate', short_desc='M/B Gate', bundle_name='mb_gate', price=40*100000 where name='lsp-plugins-mb-gate';
UPDATE product set description='Multiband Limiter', short_desc='M/B Limiter', bundle_name='mb_limiter', price=40*100000 where name='lsp-plugins-mb-limiter';
UPDATE product set description='Multiband Ring-Modulated Sidechain', short_desc='M/B RingMod Sidechain', bundle_name='mb_ringmod_sc', price=25*100000 where name='lsp-plugins-mb-ringmod-sc';
UPDATE product set description='Parametric Equalizer', short_desc='Parametric Equalizer', bundle_name='para_equalizer', price=40*100000 where name='lsp-plugins-para-equalizer';
UPDATE product set description='Phaser', short_desc='Phaser', bundle_name='phaser', price=20*100000 where name='lsp-plugins-phaser';
UPDATE product set description='Referencer', short_desc='Referencer', bundle_name='referencer', price=40*100000 where name='lsp-plugins-referencer';
UPDATE product set description='Return', short_desc='Return', bundle_name='return', price=0*100000 where name='lsp-plugins-return';
UPDATE product set description='Ring-Modulated Sidechain', short_desc='Ring-Modulated Sidechain', bundle_name='ringmod_sc', price=20*100000 where name='lsp-plugins-ringmod-sc';
UPDATE product set description='Room Builder', short_desc='Room Builder', bundle_name='room_builder', price=60*100000 where name='lsp-plugins-room-builder';
UPDATE product set description='Sampler', short_desc='Sampler', bundle_name='sampler', price=30*100000 where name='lsp-plugins-sampler';
UPDATE product set description='Send', short_desc='Send', bundle_name='send', price=0*100000 where name='lsp-plugins-send';
UPDATE product set description='Trigger', short_desc='Trigger', bundle_name='trigger', price=20*100000 where name='lsp-plugins-trigger';

