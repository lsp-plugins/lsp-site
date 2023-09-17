<h1>Consumer survey from LSP Project</h1>

<p>As you probably know, LSP Project got first working builds of plugins for Windows.</p>
<p>Despite we're not planning to distribute them for free for Windows platform while keeping Linux and FreeBSD builds free.</p>
<p>The main reason is, if people use proprietary solutions, then it is pretty fair to ask money for the job that developers of LSP project already did for almost 8 years of the project activity. And we still keep going on!</p>
<p>To understand the value of LSP Project on the consumer market and estimate the fair price for the sofware, we ask you to complete this short survey.</p>
<p>The survey is completely anonymous.</p>

<form id="fb_form" action="<?=$SITEROOT?>/lsp_market_survey.php" method="POST">

<h2>Please specify your gender</h2>
<p><input type="radio" id="gender_m" name="gender" value="m"><label for="gender_m">Male</label></p>
<p><input type="radio" id="gender_f" name="gender" value="f"><label for="gender_f">Female</label></p>
<p><input type="radio" id="gender_o" name="gender" value="o"><label for="gender_o">Other</label></p>

<h2>What is your work status?</h2>
<p><input type="radio" id="status_stud" name="status" value="stud"><label for="status_stud">Student</label></p>
<p><input type="radio" id="status_teach" name="status" value="teach"><label for="status_teach">Teacher/Mentor</label></p>
<p><input type="radio" id="status_free" name="status" value="free"><label for="status_free">Freelancer</label></p>
<p><input type="radio" id="status_fact" name="status" value="fact"><label for="status_fact">Employer/Manufacturer</label></p>
<p><input type="radio" id="status_emp" name="status" value="emp"><label for="status_emp">Employed</label></p>
<p><input type="radio" id="status_unemp" name="status" value="unemp"><label for="status_unemp">Unemployed</label></p>
<p><input type="radio" id="status_pens" name="status" value="pens"><label for="status_pens">Pensioner</label></p>
<p><input type="radio" id="status_none" name="status" value="none"><label for="status_none">None of above</label></p>

<h2>What roles match to your relation to music production?</h2>
<p><input type="checkbox" id="role_lst" name="role" value="lst"><label for="role_lst">Listener</label></p>
<p><input type="checkbox" id="role_ent" name="role" value="ent"><label for="role_ent">Enthusiast</label></p>
<p><input type="checkbox" id="role_dj" name="role" value="dj"><label for="role_dj">DJ</label></p>
<p><input type="checkbox" id="role_mus" name="role" value="mus"><label for="role_mus">Musician</label></p>
<p><input type="checkbox" id="role_dan" name="role" value="dan"><label for="role_dan">Dancer</label></p>
<p><input type="checkbox" id="role_act" name="role" value="act"><label for="role_act">Actor</label></p>
<p><input type="checkbox" id="role_cri" name="role" value="cri"><label for="role_cri">Critic</label></p>
<p><input type="checkbox" id="role_rev" name="role" value="rev"><label for="role_rev">Reviewer</label></p>
<p><input type="checkbox" id="role_jour" name="role" value="jour"><label for="role_jour">Journalist</label></p>
<p><input type="checkbox" id="role_eng" name="role" value="eng"><label for="role_eng">Audio Engineer</label></p>
<p><input type="checkbox" id="role_prod" name="role" value="prod"><label for="role_prod">Producer</label></p>
<p><input type="checkbox" id="role_adv" name="role" value="adv"><label for="role_adv">Advertiser</label></p>
<p><input type="checkbox" id="role_lbl" name="role" value="lbl"><label for="role_lbl">Label</label></p>
<p><input type="checkbox" id="role_sw_dev" name="role" value="sw_dev"><label for="role_sw_dev">Developer of music software</label></p>
<p><input type="checkbox" id="role_hw_dev" name="role" value="hw_dev"><label for="role_hw_dev">Developer of sound-related hardware devices</label></p>
<p><input type="checkbox" id="role_oth" name="role" value="oth"><label for="role_oth">Other</label></p>

<h2>What is your money income from music?</h2>
<p><input type="radio" id="income_none" name="income" value="none"><label for="income_none">I don't have any income</label></p>
<p><input type="radio" id="income_rare" name="income" value="rare"><label for="income_rare">Rare/variable income</label></p>
<p><input type="radio" id="income_small" name="income" value="small"><label for="income_small">Small income</label></p>
<p><input type="radio" id="income_sat" name="income" value="sat"><label for="income_sat">Satisfactory income</label></p>
<p><input type="radio" id="income_high" name="income" value="high"><label for="income_high">Higher than satisfactory income</label></p>
<p><input type="radio" id="income_huge" name="income" value="huge"><label for="income_huge">Very high income</label></p>

<h2>Are you familiar with the <a href="<?=$SITEROOT?>/" target="_blank">Linux Studio Plugins project</a>?</h2>
<p><input type="radio" id="familiar_no" name="familiar" value="no"><label for="familiar_no">No, never heard about it previously</label></p>
<p><input type="radio" id="familiar_some" name="familiar" value="some"><label for="familiar_some">Heard something but never tried</label></p>
<p><input type="radio" id="familiar_know" name="familiar" value="know"><label for="familiar_know">Know about it but don't have possibility to use it</label></p>
<p><input type="radio" id="familiar_other" name="familiar" value="other"><label for="familiar_other">Know about it but prefer other tools</label></p>
<p><input type="radio" id="familiar_rare" name="familiar" value="rare"><label for="familiar_rare">Rarely use some of it's stuff in my work</label></p>
<p><input type="radio" id="familiar_regular" name="familiar" value="regular"><label for="familiar_regular">Regularly use some of it's stuff in my work</label></p>
<p><input type="radio" id="familiar_always" name="familiar" value="always"><label for="familiar_always">Use it's stuff almost in every my project</label></p>

<h2>According to your opinion, what is the fair price range for the
<a href="<?=$SITEROOT?>/?page=manuals&section=para_equalizer_x32_stereo" target="_blank">LSP Parametric Equalizer plugin series</a>
(<a href="https://youtu.be/TfpJPsiouuU" target="_blank">Demo on YouTube</a>)?</h2>
<p><input type="radio" id="para_eq_10" name="para_eq" value="10"><label for="para_eq_10">Not more than $10</label></p>
<p><input type="radio" id="para_eq_20" name="para_eq" value="20"><label for="para_eq_20">$11-$20</label></p>
<p><input type="radio" id="para_eq_30" name="para_eq" value="30"><label for="para_eq_30">$21-$30</label></p>
<p><input type="radio" id="para_eq_40" name="para_eq" value="40"><label for="para_eq_40">$31-$40</label></p>
<p><input type="radio" id="para_eq_50" name="para_eq" value="50"><label for="para_eq_50">$41-$50</label></p>
<p><input type="radio" id="para_eq_60" name="para_eq" value="60"><label for="para_eq_60">$51-$60</label></p>
<p><input type="radio" id="para_eq_70" name="para_eq" value="70"><label for="para_eq_70">$61-$70</label></p>
<p><input type="radio" id="para_eq_80" name="para_eq" value="80"><label for="para_eq_80">$71-$80</label></p>
<p><input type="radio" id="para_eq_90" name="para_eq" value="90"><label for="para_eq_90">$81-$90</label></p>
<p><input type="radio" id="para_eq_100" name="para_eq" value="100"><label for="para_eq_100">$91-$100</label></p>
<p><input type="radio" id="para_eq_custom" name="para_eq" value="custom"><label for="">More than $100 (please specify):</label>&nbsp;<input type="text" name="para_eq_custom" value="" /></p>

<h2>According to your opinion, what is the fair price range for the
<a href="<?=$SITEROOT?>/?page=manuals&section=mb_compressor_stereo" target="_blank">LSP Multiband Compressor plugin series</a>
(<a href="https://youtu.be/RCdk94Hta3o" target="_blank">Demo on YouTube</a>)?</h2>
<p><input type="radio" id="mb_comp_10" name="mb_comp" value="10"><label for="mb_comp_10">Not more than $10</label></p>
<p><input type="radio" id="mb_comp_20" name="mb_comp" value="20"><label for="mb_comp_20">$11-$20</label></p>
<p><input type="radio" id="mb_comp_30" name="mb_comp" value="30"><label for="mb_comp_30">$21-$30</label></p>
<p><input type="radio" id="mb_comp_40" name="mb_comp" value="40"><label for="mb_comp_40">$31-$40</label></p>
<p><input type="radio" id="mb_comp_50" name="mb_comp" value="50"><label for="mb_comp_50">$41-$50</label></p>
<p><input type="radio" id="mb_comp_60" name="mb_comp" value="60"><label for="mb_comp_60">$51-$60</label></p>
<p><input type="radio" id="mb_comp_70" name="mb_comp" value="70"><label for="mb_comp_70">$61-$70</label></p>
<p><input type="radio" id="mb_comp_80" name="mb_comp" value="80"><label for="mb_comp_80">$71-$80</label></p>
<p><input type="radio" id="mb_comp_90" name="mb_comp" value="90"><label for="mb_comp_90">$81-$90</label></p>
<p><input type="radio" id="mb_comp_100" name="mb_comp" value="100"><label for="mb_comp_100">$91-$100</label></p>
<p><input type="radio" id="mb_comp_custom" name="mb_comp" value="custom"><label for="">More than $100 (please specify):</label>&nbsp;<input type="text" name="mb_comp_custom" value="" /></p>

<h2>According to your opinion, what is the fair price for the 
<a href="<?=$SITEROOT?>/?page=manuals&section=flanger_stereo">LSP Flanger plugin series</a>
(<a href="https://youtu.be/_WD9GndORQA" target="_blank">Demo on YouTube</a>)?</h2>
<p><input type="radio" id="flanger_10" name="flanger" value="10"><label for="flanger_10">Not more than $10</label></p>
<p><input type="radio" id="flanger_20" name="flanger" value="20"><label for="flanger_20">$11-$20</label></p>
<p><input type="radio" id="flanger_30" name="flanger" value="30"><label for="flanger_30">$21-$30</label></p>
<p><input type="radio" id="flanger_40" name="flanger" value="40"><label for="flanger_40">$31-$40</label></p>
<p><input type="radio" id="flanger_50" name="flanger" value="50"><label for="flanger_50">$41-$50</label></p>
<p><input type="radio" id="flanger_60" name="flanger" value="60"><label for="flanger_60">$51-$60</label></p>
<p><input type="radio" id="flanger_70" name="flanger" value="70"><label for="flanger_70">$61-$70</label></p>
<p><input type="radio" id="flanger_80" name="flanger" value="80"><label for="flanger_80">$71-$80</label></p>
<p><input type="radio" id="flanger_90" name="flanger" value="90"><label for="flanger_90">$81-$90</label></p>
<p><input type="radio" id="flanger_100" name="flanger" value="100"><label for="flanger_100">$91-$100</label></p>
<p><input type="radio" id="flanger_custom" name="flanger" value="custom"><label for="">More than $100 (please specify):</label>&nbsp;<input type="text" name="flanger_custom" value="" /></p>

<h2>According to your opinion, what is the fair price for the bundle of
<a href="<?=$SITEROOT?>/?page=manuals&section=phase_detector">LSP Phase Detector plugin</a> and
<a href="<?=$SITEROOT?>/?page=manuals&section=comp_delay_stereo">Delay Compensator plugin series</a>
(<a href="https://youtu.be/j-rNb409GYg" target="_blank">Demo on YouTube</a>).</h2>
<p><input type="radio" id="pd_delay_10" name="pd_delay" value="10"><label for="pd_delay_10">Not more than $10</label></p>
<p><input type="radio" id="pd_delay_20" name="pd_delay" value="20"><label for="pd_delay_20">$11-$20</label></p>
<p><input type="radio" id="pd_delay_30" name="pd_delay" value="30"><label for="pd_delay_30">$21-$30</label></p>
<p><input type="radio" id="pd_delay_40" name="pd_delay" value="40"><label for="pd_delay_40">$31-$40</label></p>
<p><input type="radio" id="pd_delay_50" name="pd_delay" value="50"><label for="pd_delay_50">$41-$50</label></p>
<p><input type="radio" id="pd_delay_60" name="pd_delay" value="60"><label for="pd_delay_60">$51-$60</label></p>
<p><input type="radio" id="pd_delay_70" name="pd_delay" value="70"><label for="pd_delay_70">$61-$70</label></p>
<p><input type="radio" id="pd_delay_80" name="pd_delay" value="80"><label for="pd_delay_80">$71-$80</label></p>
<p><input type="radio" id="pd_delay_90" name="pd_delay" value="90"><label for="pd_delay_90">$81-$90</label></p>
<p><input type="radio" id="pd_delay_100" name="pd_delay" value="100"><label for="pd_delay_100">$91-$100</label></p>
<p><input type="radio" id="pd_delay_custom" name="pd_delay" value="custom"><label for="">More than $100 (please specify):</label>&nbsp;<input type="text" name="pd_delay_custom" value="" /></p>

<div data-theme="dark" class="g-recaptcha" data-sitekey="<?= $GOOGLE['recaptcha_pub'] ?>"></div>

<div class="fs-send">
	<input type="submit" value="Complete the survey">
</div>

</form>
