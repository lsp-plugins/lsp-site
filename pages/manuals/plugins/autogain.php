<?php
	plugin_header();
	$m      =   ($PAGE == 'autogain_mono') ? 'm' : 's';
	$sc     =   (strpos($PAGE, 'sc_') === 0);
?>

<p>
	This plugin provides automatic gain control tools that support loudness matching
	according to the <a href="https://www.itu.int/rec/R-REC-BS.1770-4-201510-I/en">ITU-R BS.1770-4 recommendation</a>.
	That means that the input signal is being processed via the weighting filter (K-filter) before the measurement
	of it's energy is performed. According to the BS.1770-4 recommendation, the energy measurement period should be of
	400 milliseconds but the plugin allows to vary this value. As an additional option, it also provides other kind of
	weighting filters provided by the the <a href="https://webstore.iec.ch/publication/5708">IEC 61672:2003</a>
	standard: A, B, C and D weighting filters.
</p>
<?php if($sc) { ?>
<p>
	Additional sidechain input allows to gain the control over the loudness of the output in two different modes:
</p>
<ul>
	<li>Control</li> - the loudness level of the sidechain signal is measured, then the corresponding gain correction is
	computed to match the loudness of the sidechain signal to the desired loudness level, and the computed gain is
	applied to the original track.
	<li>Match</li> - the loudness level of the sidechain signal is measured, then the measured loudness is interpreted
	as a desired loudness level. After that, the loudness of the input signal is measured, then the corresponding gain
	correction is computed to match the loudness level of the input signal to the loudness level of the sidechain signal.
</ul>
<?php } ?>
<p>
	If the level of the signal rapidly changes, the plugin can also rapidly reduce the shocking effect of the loud sound
	and (if enabled) rapidly raise the gain, too. Despite, because it uses RMS measurements, it does not fully protect from
	sudden loud clicks/pops, and additional surge protection should be applied. Additional control over the zero level also
	makes the plugin act as a trigger: if the signal is below the mimum level, then the gain value does not change.
	This prevents from significant amplification of a background noise in the case of long silence at the input. 
</p>

<p><b>Controls:</b></p>
<ul>
	<li><b>Bypass</b> - bypass switch, when turned on the plugin bypasses signal without any change.</li>
	<li><b>Weighting</b> - weighting function for the signal:</li>
	<ul>
		<li>None - no weighting applied.</li>
		<li>IEC 61672:2003 A filter - A filter defined by IEC 61672:2003 standard.</li>
		<li>IEC 61672:2003 B filter - B filter defined by IEC 61672:2003 standard.</li>
		<li>IEC 61672:2003 C filter - C filter defined by IEC 61672:2003 standard.</li>
		<li>IEC 61672:2003 D filter - D filter defined by IEC 61672:2003 standard.</li>
		<li>ITU-R BS.1770-4 K filter - K filter defined by ITU-R BS.1770-4 standard.</li>
	</ul>
	<li><b>SC Mode</b> - sidechain mode:</li>
	<ul>
		<li>Internal</li> - the input signal is also fed to the sidechain.
	<?php if($sc) { ?>
		<li>Control SC</li> - the level of sidechain input signal and desired level of loudness are used co compute gain correction.
		<li>Match SC</li> - the level of the input signal is corrected to match the level of the sidechain input signal.
		<li>Control Link</li> - the level of the input signal passed by shared memory link and desired level of loudness are used co compute gain correction.
		<li>Match Link</li> - the level of the input signal is corrected to match the level of the input signal passed by shared memory link.
	<?php } ?>
	</ul>
	<li><b>Link</b> - the name of the shared memory link to pass control/match signal.</li>
</ul>

<p><b>Meters</b>:</p>
<ul>
	<li><b>In</b> - the loudness (measured for short and long periods) of the input signal in LUFS/LKFS units.</li>
	<li><b>Sc</b> - the loudness (measured for short and long periods) of the sidechain signal in LUFS/LKFS units.</li>
	<li><b>Gan</b> - the level of the gain correction signal.</li>
	<li><b>Out</b> - the loudness (measured for short and long periods) of the output signal in LUFS/LKFS units.</li>
</ul>

<p><b>Sidechain</b> Section:</p>
<ul>
	<li><b>Preamp</b> - additional gain applied to the sidechain signal.</li>
	<li><b>Lookahead</b> - sidechain lookahead time, allows to delay the original signal to make work more smooth.</li>
</ul>
<p><b>Loudness</b> Section:</p>
<ul>
	<li><b>Level</b> - the desired loudness level of the signal.</li>
	<li><b>Drift</b> - the maximum difference between the long-time and short-time loudness levels before the automatic gain
	control starts reacting rapidly.</li>
	<li><b>Silence</b> - the threshold of the silence level. All sounds with loudness below this threshold are considered to be silence.</li>
	<li><b>Max gain</b> - allows to limit the maximum possible gain amplificaiton applied to the signal.</li>
</ul>
<p><b>Long-time processing</b> Section:</p>
<ul>
	<li><b>Period</b> - the measurement period of time for long-time loudness measurements.</li>
	<li><b>Fall</b> - controls that allow to configure speed of the gain fall when the gain correction
	needs to be lowered according to the changes of the long-time loudness measurements.</li>
	<li><b>Grow</b> - controls that allow to configure speed of the gain grow when the gain correction
	needs to be raised according to the changes of the long-time loudness measurements.</li>
</ul>
<p><b>Short-time processing</b> Section:</p>
<ul>
	<li><b>Period</b> - the measurement period of time for short-time loudness measurements.</li>
	<li><b>Fall</b> - controls that allow to configure speed of the gain fall when the gain correction
	needs to be rapidly lowered according to the changes of the short-time loudness measurements.</li>
	<li><b>Grow</b> - controls that allow to enable and configure speed of the gain grow when the gain correction
	needs to be rapidly raised according to the changes of the short-time loudness measurements.</li>
</ul>

