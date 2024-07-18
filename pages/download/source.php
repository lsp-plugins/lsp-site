<?php

$artifacts = utl_map_by_field($latest_artifacts['any'], 'format');

$src_artifact = $artifacts['src'][0];
$latest_version = implode('.', $src_artifact['version']);
$artifact_file = $src_artifact['file'];

$source_link = htmlspecialchars("{$CODE_REPO}/releases/download/{$latest_version}/{$artifact_file}");
?>

<h2>Source code</h2>

<p>Source code is available under terms of <a href="https://www.gnu.org/licenses/lgpl-3.0.en.html" alt="GNU LGPLv3">GNU Lesser Public License version 3</a></p>

<p>You can download source code directly by following link: </p>
<p> <a class="download-text-button" href="<?= $source_link ?>" alt="Source code" target="_blank">Source code</a></p> 

<p>The work on the source code is performed on GitHub: <a href="<?= $CODE_REPO; ?>" alt="LSP Plugins Organization">LSP Plugins Organization</a>.</p>

<h2>Building</h2>
<?php file_content($README_FILE, 'building'); ?>
