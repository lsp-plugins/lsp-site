<?php
	plugin_header();
	
	$sc     =   (strpos($PAGE, 'sc_') === 0);
	$m      =   (strpos($PAGE, '_mono') > 0) ? 'm' : (
		(strpos($PAGE, '_stereo') > 0) ? 's' : (
			(strpos($PAGE, '_lr') > 0) ? 'lr' : (
				(strpos($PAGE, '_ms') > 0) ? 'ms' : '?'
				)));
	$cc     =   ($m == 'm') ? 'mono' : 'stereo';
	$sm     =   ($m == 'ms') ? ' M, S' : (($m != 'm') ? ' L, R' : '');
?>

<p>
	This plugin performs multiband dynamic processing of <?= $cc ?> input signal<?php 
	if ($m == 'ms') echo " in Mid-Side mode";
	elseif ($m == 'lr') echo " by applying individual processing to left and right channels separately";
	?>. Flexible sidechain-control configuration <?php
		if ($sc)
			echo " and additional sidechain input" . (($m == 'm') ? '' : 's') . " are ";
		else
			echo " is";
	?>provided.
</p>
<p>
	This dynamic processor provides numerous special functions listed below:
</p>
<ul>
	<li><b>Modern operating mode</b> - special operating mode that allows to look different at <b>classic</b> crossover-based devices.
	Crossover-based devices use crossover filters for splitting the original signal into independent frequency bands, then process
	each band independently by it's individual gate. Finally, all bands become phase-compensated using all-pass filers and then
	summarized, so the output signal is formed.
	In <b>Modern</b> mode, each band is processed by pair of dynamic shelving filters. This allows the better control the gain of each band.
	</li>
	<li><b>Linear Phase</b> mode allows to split audio signal into multiple frequency bands with linear phase shift.
	This introduces additional latency but gives several benefits:</li>
	<ul>
		<li>Unlike classic crossovers which use IIR (Infinite Impulse Response) filters to split signal into multiple bands and shift the phase
		of the audio signal at band split points, the <b>Linear Phase</b> allows to use FIR (Finite Impulse Response) filters which are deprived of this.
		<li>Unlike most IIR filters which are designed using bilinear transform, linear phase filters allow to simulate their transfer function
		to look like the transfer function of analog filters, without deforming it's magnitude envelope near the nyquist frequency.</li>
		<li>Unlike design of classic Linkwitz-Riley filters, the design of IIR filters provides shorter transition zone of the filter.</li>
	</ul>
	<li><b>Sidechain boost</b> - special mode for assigning the same weight for higher frequencies opposite to lower frequencies.
	In usual case, the frequency band is processed by gate 'as is'. By the other side, the usual audio signal has 3 db/octave
	falloff in the frequency domain and could be compared with the pink noise. So the lower frequencies take more
	effect on gate rather than higher frequencies. <b>Sidechain boost</b> feature allows to compensate the -3 dB/octave falloff
	of the signal spectrum and, even more, make the signal spectrum growing +3 dB/octave in the almost fully audible frequency range.
	This is done by specially designed +3 db/oct and +6 db/oct shelving filters.
	</li>
	<li><b>Lookahead option</b> - each band of gate can work with some prediction, the lookahead time can be set for each channel independently.
	To avoid phase distortions, all other bands automatically become delayed for a individually calculated period of time. The overall delay time
	of the input signal is reported to the host by the plugin as a latency.
	</li>
	<li><b>Up to 8 bands</b> are available for use, each band is not attached to it's strict frequency range and can control any frequency range. 
	Also, each band can be controlled by completely different frequency range that can be obtained by applying low-pass and hi-pass filters to the
	sidechain signal. 
	</li>
	<?php if ($m == 's') { ?>
	<li><b>Stereo split mode</b> allows to apply processing to the left and right channels independently.</li>
	<?php } ?>
</ul>
<p><b>Controls:</b></p>
<ul>
	<li>
		<b>Bypass</b> - bypass switch, when turned on (led indicator is shining), the plugin bypasses signal.
	</li>
	<li><b>Mode</b> - combo box that allows to switch between the following modes:</li>
	<ul>
		<li><b>Classic</b> - classic operating mode using IIR filters and allpass filters to compensate phase shifts.</li>
		<li><b>Modern</b> - modern operating mode using IIR shelving filters to adjust the gain of each frequency band.</li>
		<li><b>Linear Phase</b> - linear phase operating mode using FFT transform (FIR filters) to split signal into multiple bands, introduces additional latency.</li>
	</ul>
	<li><b>SC Boost</b> - enables addidional boost of the sidechain signal:</li>
	<ul>
		<li><b>None</b> - no sidechain boost is applied.</li>
		<li><b>Pink BT</b> - a +3db/octave sidechain boost using bilinear-transformed shelving filter.</li>
		<li><b>Pink MT</b> - a +3db/octave sidechain boost using matched-transformed shelving filter.</li>
		<li><b>Brown BT</b> - a +6db/octave sidechain boost using bilinear-transformed shelving filter.</li>
		<li><b>Brown MT</b> - a +6db/octave sidechain boost using matched-transformed shelving filter.</li>
	</ul>
	<li><b>Link</b> - the name of shared memory link used to receive sidechain signal</li>
	<li><b>FFT<?= $sm ?> In</b> - enables FFT curve graph of input signal on the spectrum graph.</li>
	<li><b>FFT<?= $sm ?> Out</b> - enables FFT curve graph of output signal on the spectrum graph.</li>
	<li><b>Filters<?= $sm ?></b> - enables drawing transfer function of each sidechain filter on the spectrum graph.</li>
	<?php if ($m == 's') { ?>
	<li><b>Stereo Split</b> - enables independent processing of left and right channels.</li>
	<?php } ?>
	<li><b>Zoom</b> - zoom fader, allows to adjust zoom on the frequency chart.</li>
</ul>
<p><b>'Signal' section:</b></p>
<ul>
	<li><b>Input</b> - the amount of gain applied to the input signal before processing.</li>
	<li><b>Output</b> - the amount of gain applied to the output signal before processing.</li>
	<li><b>Dry</b> - the amount of dry (unprocessed) signal passed to the output.</li>
	<li><b>Wet</b> - the amount of wet (processed) signal passed to the output.</li>
	<li><b>Dry/Wet</b> - the knob that controls the balance between the mixed dry and wet signal (see <b>Dry</b> and <b>Wet</b>) and the dry (unprocessed) signal.</li>
	<li><b>In</b> - the input signal meter.</li>
	<li><b>Out</b> - the output signal meter.</li>
</ul>
<p><b>'Analysis' section:</b></p>
<ul>
	<li><b>Reactivity</b> - the reactivity (smoothness) of the spectral analysis.</li>
	<li><b>Shift</b> - allows to adjust the overall gain of the analysis.</li>
</ul>
<?php if ($m == 'lr') {?>
	<p><b>'Split Left' and 'Split Right' sections</b> - allow to quickly control all bands and provides most frequently
	used controls for Left and Right channels of stereo signal independently:</p>
<?php } elseif ($m == 'ms') {?>
	<p><b>'Split Mid' and 'Split Side' sections</b> - allow to quickly control all bands and provides most frequently
	used controls for Middle and Side parts of stereo signal independently:</p>
<?php } else {?>
	<p><b>'Split' section</b> - allows to quickly control all bands and provides most frequently used controls:</p>
<?php }?>
<ul>
	<li><b>Band</b> - allows to enable the corresponding band, band #0 is <b>always</b> enabled.</li>
	<li><b>Range</b> - allows to adjust the start frequency of the frequency range controlled by the band.</li>
	<li><b>Controls</b> - set of buttons that control the behaviour of the compressor:</li>
	<ul>
		<li><b>On</b> - enables compressor assigned to the corresponding frequency band.</li>
		<li><b>S</b> - turns on soloing mode to the selected band by applying -36 dB gain to non-soloing bands</li>
		<li><b>M</b> - turns on muting mode to the selected band by applying -36 dB gain to it</li>
		<li><b>Sidechain combo</b> - allows to select external sidechain inputs or shared memory audio stream</li>
	</ul>
	<li><b>SC Preamp</b> - applies additional gain to the sidechain band.</li>
	<li><b>Makeup</b> - applies additional gain to the output of the corresponding dynamic processor.</li>
	<li><b>Attack Time</b> - attack time of the compressor.</li>
	<li><b>Release Time</b> - release time of the compressor.</li>
	<li><b>Ratio Low</b> - the compression/expansion ratio of the curve at the low level of signal.</li>
	<li><b>Ratio High</b> - the compression/expansion ratio of the curve at the high level of signal.</li>
	<li><b>Threshold</b> - the settings for each of four available thresholds for the corresponding dynamic processor.</li>
</ul>
<?php if ($m == 'lr') {?>
	<p><b>'Band N' section</b> - allows to simultaneously control all parameters of dynamic processor of Left and Right
	channels assigned to the selected frequency band:</p>
<?php } elseif ($m == 'ms') {?>
	<p><b>'Band N' section</b> - allows to simultaneously control all parameters of dynamic processor of Middle and Side
	channels assigned to the selected frequency band:</p>
<?php } else {?>
	<p><b>'Band N' section</b> - allows to control all parameters for the selected frequency band:</p>
<?php }?>
<ul>
	<li><b>Sidechain Source</b> - allows to set the sidechain source</li>
	<ul>
		<li><b>Internal</b> - the audio inputs of plugin are used as sidechain signal.</li>
		<?php if ($sc) { ?>
			<li><b>External</b> - additional sidechain audio inputs of plugins are used as sidechain signal.</li>
		<?php } ?>
		<li><b>Link</b> - the shared memory link is used to receive sidechain signal.</li>
	</ul>
	<li><b>Sidechain Mode</b> - combo box that allows to control sidechain working mode:</li>
	<ul>
		<li><b>Peak</b> - peak mode.</li>
		<li><b>RMS</b> - Root Mean Square (SMA) of the input signal.</li>
		<li><b>LPF</b> - input signal processed by recursive 1-pole Low-Pass Filter (LPF).</li>
		<li><b>SMA</b> - input signal processed by Simple Moving Average (SMA) filter.</li>
		<?php if ($m != 'm') { ?>
			<li><b>Middle</b> - middle part of signal is used for sidechain processing.</li>
			<li><b>Side</b> - side part of signal is used for sidechain processing.</li>
			<li><b>Left</b> - only left channel is used for sidechain processing.</li>
			<li><b>Right</b> - only right channel is used for sidechain processing.</li>
			<li><b>Min</b> - the absolute minimum value is taken from stereo input.</li>
			<li><b>Max</b> - the absolute maximum value is taken from stereo input.</li>
		<?php } ?>
		<?php if ($m == 's') { ?>
			<li><b>Left/Right</b> - left and right channels are being processing using respectively the left and right sidechain channels in stereo split mode.</li>
			<li><b>Right/Left</b> - left and right channels are being processing using respectively the right and left sidechain channels in stereo split mode.</li>
			<li><b>Mid/Side</b> - left and right channels are being processing using respectively the middle and side parts of sidechain signal in stereo split mode.</li>
			<li><b>Side/Mid</b> - left and right channels are being processing using respectively the side and middle parts of sidechain signal in stereo split mode.</li>
		<?php } ?>
	</ul>
	<li><b>Sidechain Lookahead</b> - look-ahead time of the sidechain relative to the input signal.</li>
	<li><b>Sidechain Preamp</b> - pre-amplification of the sidechain signal.</li>
	<li><b>Sidechain Reactivity</b> - reactivity of the sidechain signal.</li>
	<li><b>Sidechain LCF</b> - button turns on the low-cut filter for the sidechain signal and knob allows to control the frequency of the filter.</li>
	<li><b>Sidechain HCF</b> - button turns on the high-cut filter for the sidechain signal and knob allows to control the frequency of the filter.</li>

	<?php if ($m == 'lr') {?>
		<li><b>Dynamic Processor Left/Right On</b> - enables corresponding dynamic processor assigned to the corresponding frequency band.</li>
		<li><b>Dynamic Processor Left/Right Solo</b> - turns on soloing mode to the selected band by applying -36 dB gain to non-soloing bands.</li>
		<li><b>Dynamic Processor Left/Right Mute</b> - turns on muting mode to the selected band by applying -36 dB gain to it.</li>
	<?php } elseif ($m == 'ms') {?>
		<li><b>Dynamic Processor Mid/Side On</b> - enables corresponding dynamic processor assigned to the corresponding frequency band.</li>
		<li><b>Dynamic Processor Mid/Side Solo</b> - turns on soloing mode to the selected band by applying -36 dB gain to non-soloing bands.</li>
		<li><b>Dynamic Processor Mid/Side Mute</b> - turns on muting mode to the selected band by applying -36 dB gain to it.</li>
	<?php } else {?>
		<li><b>Dynamic Processor On</b> - enables dynamic processor assigned to the corresponding frequency band.</li>
		<li><b>Dynamic Processor Solo</b> - turns on soloing mode to the selected band by applying -36 dB gain to non-soloing bands.</li>
		<li><b>Dynamic Processor Mute</b> - turns on muting mode to the selected band by applying -36 dB gain to it.</li>
	<?php }?>
	<?php if ($m == 'lr') {?>
		<li><b>Dynamic Processor Left</b> - additionally draws the sharpened curve model for the left channel.</li>
		<li><b>Dynamic Processor Right</b> - additionally draws the sharpened curve model for the right channel.</li>		
	<?php } elseif ($m == 'ms') {?>
		<li><b>Dynamic Processor Mid</b> - additionally draws the sharpened curve model for the middle channel.</li>
		<li><b>Dynamic Processor Side</b> - additionally draws the sharpened curve model for the side channel.</li>
	<?php } else {?>
		<li><b>Dynamic Processor Model</b> - additionally draws the sharpened curve model</li>
	<?php }?>
	<li><b>Dynamic Processor Ratio Low</b> - the compression/expansion ratio of the curve at the low level of signal.</li>
	<li><b>Dynamic Processor Ratio High</b> - the compression/expansion ratio of the curve at the high level of signal.</li>
	<li><b>Dynamic Processor Makeup</b> - additional amplification gain after processing stage.</li>
	<li><b>Dynamic Processor Attack</b> - default attack time of the dynamic processor.</li>
	<li><b>Dynamic Processor Release</b> - default release time of the dynamic processor.</li>
	<li><b>Dynamic Processor Hold</b> - the time period the envelope holds it's maximum value before starting the release.</li>
	<li><b>Dynamic Processor Gain</b> - the amount of gain applied to frequency band by the compression curve.</li>
	<li><b>Dynamic Processor Ranges</b> - allows to configure up to four additional knees, attack and release ranges:</li>
	<ul>
		<li><b>Thr</b> - Knob that enables additional knee.</li>
		<li><b>Att</b> - Knob that enables additional attack range.</li>
		<li><b>Rel</b> - Knob that enables additional release range.</li>
		<li><b>Thresh</b> - Threshold of the additional knee, works only if corresponding <b>Thr</b> button is turned on.</li>
		<li><b>Gain</b> - Gain of the additional knee, works only if corresponding <b>Thr</b> button is turned on.</li>
		<li><b>Knee</b> - Softness of the knee, works only if corresponding <b>Thr</b> button is turned on.</li>
		<li><b>Attack</b> - Pair of knobs that allows to adjust the attack threshold and attack time of the additional
			attack range. The new attack time is applied if the envelope is <b>over</b> the specified threshold. Otherwise
			the attack time of previous range or default attack time (if there is no previous range) will be applied.
		</li>
		<li><b>Release</b> - Pair of knobs that allows to adjust the release threshold and release time of the additional
			release range. The new release time is applied if the envelope is <b>over</b> the specified threshold. Otherwise
			the release time of previous range or default release time (if there is no previous range) will be applied.
		</li>
	</ul>
</ul>
