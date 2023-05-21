<h1>NEWS</h1>

<h3>2023-05-21</h3>

<p>LSP Plugins 1.2.7 released.</p>
<ul>
<li>Fixed CLAP UI support for Bitwig Studio.</li>
<li>Fixed build that disabled CairoCanvas for Inline Display feature.</li>
<li>Fixed memory leakage issued by the libcairo font cache on the plugin UI close. Moved custom font rendering solution to direct usage of libfreetype.</li>
<li>Fixed crash (BadMatch) for Fluxbox window manager on popup windows.</li>
<li>Fixed popup window heading display in Fluxbox.</li>
<li>Fixed non-working solo button for Parametric Equalizer after 1.2.6 release.</li>
<li>Removed Makefile.d. Dependencies are now automatically generated at the build stage.</li>
<li>Added possibility to invert the behaviour of mouse scroll for all widgets or for graph dot widget.</li>
<li>Added frequency display and gain for each filter of Graphic Equalizer plugin series.</li>
<li>Added measure button and meter line on graph for Spectrum Analyzer plugin series.</li>
<li>Added display of filter number, filer channel and filter type near to the frequency and note.</li>
<li>Added possibility to change the thickness of lies on the spectrum graph for the Spectrum Analyzer plugin series.</li>
<li>Some output parameters like Latency are not exported into configuration file anymore.</li>
</ul>

<h3>2023-03-22</h3>

<p>LSP Plugins 1.2.6 released.</p>
<ul>
<li><b>Implemented Mixer plugin series for 4, 8 and 16 Mono/Stereo channels.</b></li>
<li><b>Implemented A/B Tester plugin series with blind option.</b></li>
<li>Parametric Equalizer is now smoothly operating with frequency/gain/q factor for each mode: FIR, IIR, FFT, SPM.</li>
<li>Added popup menu for more precise control over the equalizer dot on the graph for Parametric Equalizer.</li>
<li>Added frequency and note displaying (with detune in cents) over the currently configured filter in the Parametric Equalizer.</li>
<li>Implemented automatic and manual inspect mode for filters in Parametric Equalizer.</li>
<li>Implemented more user-friendly inspecting slider to the Spectrum Analyzer plugin series: 
  frequency value, amplitude and note (with detune in cents) are displayed now near the slider
  which can be adjusted with mouse movements.</li>
<li>Added basic SFZ import support by the Multisampler plugin series.</li>
<li>Added shared objects related to 3D rendering to LV2, VST and CLAP packages.</li>
<li>Fixed buggy tether for the parameter popup window. </li>
<li>Fixed improper data stream synchronization using LV2:Atom protocol.</li>
<li>Implemented linear ramping lramp_* functions in the lsp-dsp-lib optimized for i686, x86_64, ARM32 and AArch64 architectures.</li>
<li>Fixed avx::dyn_biquad_process_x8_fma3 function implementation which could cause some data distortion in the output buffer.</li>
<li>Fixed plugin state out of sync for CLAP plugin format on state restore in REAPER.</li>
<li>Several bugfixes in UI libraries.</li>
<li>Better support for build under different ARM architectures.</li>
<li>The Windows support status has been changed to 'Compiles'.</li>
<li>The AArch64 support status has been changed to 'Full'.</li>
</ul>

<h3>2023-01-29</h3>
<p>LSP Plugins 1.2.5 released.</p>
<ul>
<li>Introduced CLAP plugin format support.</li>
<li>Introduced the 'Override Hydrogen drumkit' feature for Multisampler plugin series.</li>
<li>Reworked and simplified the behaviour of the parameters that define 'Stretch' and
  'Loop' ranges.</li>
<li>Minimum and maximum values now depend on the sample length for several parameters
  like 'Stretch', 'Loop', 'Fade in' and 'Fade out' in the Sampler and Multisampler
  plugin series.</li>
<li>Fixed the application menu spam in GNOME environment by reworking the XDG files
  (contributed by sdwolfz).</li>
<li>Changed VST parameter normalized value mapping for logarithmic parameters.</li>
<li>Added possibility to specify JACK connections that should be estimated by the
  standalone JACK plugin when the plugin connects to the JACK server.</li>
<li>Implemented plugin metadata validator which runs at the build stage and verifies
  the consistency of the plugin metadata.</li>
<li>Fixed bug related to modification of cyclic parameters for several plugin formats.</li>
<li>Fixed possible crash when importing configuration data from clipboard.</li>
<li>Added possibility to enable/disable Knob&quot;s scale actions.</li>
<li>Several bugfixes related to the memory access in the lsp-ws-lib.</li>
</ul>

<h3>2022-12-21</h3>
<p>The anniversary release 1.2.4 of LSP Plugins is ready.</p>
<ul>
<li>Implemented Noise Generator plugin series.</li>
<li>Added 'Stretch' and 'Compensate' functions to the sampler plugin (contributed by Vitalius Kuchalskis).</li>
<li>Added support of 'Loop' feature by the Sampler plugin series.</li>
<li>Added audio sample preview feature in the file open dialog for Sampler, Trigger, Impulse Responses and Impulse Reverb plugin series.</li>
<li>Extended LSPC (LSP Chunk) file format to support additional types of chunks.</li>
<li>Added possibility to export Sampler configuration as a single bundle with the configuration and audio files packaged together into the LSPC file format.</li>
<li>Fixed broken configuration save and load operations with relative file paths.</li>
<li>Added 'Min' and 'Max' sidechain sources for single-band and multiband dynamic processing plugins: Compressor, Dynamic Processor, Expander, Gate.</li>
<li>Added possibility to add several filters by performing mouse double click on the graph area for the Parametric Equalizer plugin series.</li>
<li>Added several built-in presets for the Parametric Equalizer plugin series (contributed by Largos @ linuxmusicians).</li>
<li>Fixed performance regression of the UI.</li>
<li>Significant optimizations of CPU consumption by the dspu::Limiter module.</li>
<li>Introduced tab control for the lsp-tk lib and lsp-plugin-fw.</li>
<li>Raised the maximum supported sample rate from 192 kHz to 384 kHz.</li>
<li>Several bug fixes in lsp-dsp-units library.</li>
<li>Several bug fixes in lsp-runtime-lib library.</li>
<li>Several bug fixes in lsp-plugin-fw library.</li>
<li>Several bug fixes in lsp-tk-lib library.</li>
<li>Fixed 32-bit ARM architecture detection under 64-bit CPU in makefiles.</li>
</ul>

<h3>2022-09-07</h3>
<p>LSP Plugins 1.2.3 released!</p>
<ul>
<li>Graphic Toolkit Library lsp-tk-lib has been ported to Windows.</li>
<li>Updated compressor plugin bundles: added negative boosting gain option in 'Boosting' mode.</li>
<li>Updated gate plugin bundles: added negative gain option for ducking gateway option.</li>
<li>Assigned more correct names for sidechain types of dynamic processing plugins.</li>
<li>Introduced several workarounds for the support of UI by the OBS host.</li>
<li>Fixed build of i386 DSP code under FreeBSD with Clang compiler.</li>
<li>Better support of building code for ARMv6, ARMv7 and AArch64 under FreeBSD with Clang compiler.</li>
<li>Added basic CI tests introduced by GitHub CI.</li>
<li>Several bugfixes in core libraries.</li>
</ul>

<h3>2022-06-23</h3>
<p>LSP Plugins 1.2.2 released!</p>
<ul>
<li><b>Implemented Multiband Dynamic Processor plugin series.</b></li>
<li>Changed donation methods.</li>
<li>Added german translations (contributed by Johannes Guenther).</li>
<li>Added pitch control for the sample in the Sampler and Multisampler plugin series (contributed by Vitalius Kuchalskis).</li>
<li>Added pitch control for the sample in the Trigger plugin series.</li>
<li>Fixed plugin version tracking which didn't save the updated version to the configuration file.</li>
<li>Fixed improper configuration file import in JACK headless mode.</li>
<li>Fixed segmentation fault error in JACK headless mode when JACK connection was lost.</li>
<li>Added window scaling button function for plugin window.</li>
</ul>


<h3>2022-05-04</h3>
<p>Release 1.2.1</p>
<ul>
<li>Introduced JACK connection status indication for JACK plugin format.</li>
<li>Improved keyboard event handling for VST2 plugin format in the case the host prevents
  plugins from directly receiving X11 events.</li>
<li>Updated serialization format of the KVT (Key-Value Tree) for the LV2 plugin format.</li>
<li>Updated parameter mapping LV2 URI for KVT.</li>
<li>Updated build (some resources were unnecessary added to builtin resources).</li>
<li>Updated version handling in the UI wrapper that allows to control multiple bundle
  versions in one global configuration file.</li>
<li>Updated grammar in several text comments (contributed by Dennis Braun).</li>
<li>Fixed typo in Wavefront Object File Format name.</li>
<li>Fixed bugs related to usage of custom installation prefix in build scripts.</li>
<li>Fixed the XDG data installation script that forced to use '/usr/local/share'
  location for shared icons.</li>
<li>Fixed problem with the UI visibility status update for JACK plugin format
  that could cause problems of missing of some graphical content in the UI.</li>
<li>Fixed invalid pointer dereference when exporting configuration file that could cause crashes.</li>
<li>Fixed bug that caused plugins working in headless mode to crash.</li>
<li>Fixed regression related to saving state of some plugin controls using lv2:State interface.</li>
<li>Fixed missing serialization for high-precision parameters in the configuration file.</li>
<li>Fixed bug related to improper parsing of port name aliases in the UI.</li>
<li>Fixed typo in Wavefront Object File Format name.</li>
<li>Fixed bug that could cause improper window sizing in several cases.</li>
</ul>

<h3>2022-03-26</h3>

<p>Release 1.2.0.</p>
<ul>
<li>Decomposition of core modules into submodules.</li>
<li>Implemented multiple visual themes for the plugins.</li>
<li>Added possibility to change visual themes in runtime.</li>
<li>Added headless support for JACK plugins.</li>
<li>Fixed delay compensation issue for Dry/Wet balance for single-banded dynamic plugin series
  (Compressor/Gate/Expander/Dynamic Processor).</li>
<li>Add support of LV2UI:scaleFactor extension - https://github.com/drobilla/lv2/pull/38/commits</li>
<li>Implemented 'Reset to default' button.</li>
<li>Added support of building under LoongArch32 and LoongArch64 architectures.</li>
<li>Added support of building under 32-bit and 64-bit RISC-V architectures.</li>
</ul>

<h3>2021-12-21</h3>
<p>This is traditional release up to the anniversary of LSP Plugins.</p>
<p>There are no huge differences in the source code tree and the release consists mostly of user contributions.</p>
<p>But the primary goal is that the 1.1.x development branch becomes frozen until the upcoming 1.2.0 release will be ready.</p>
<p>The set of changes is the following:</p>
<ul>
<li>Fixed X11 error handling routine that could crash under certain conditions.</li>
<li>Better support for musl libc (contributed by Artur Sinila).</li>
<li>Added support of VERBOSE parameter for build system (contributed by Artur Sinila).</li>
<li>Fixed possible system crash in profiler plugin.</li>
<li>Updated LV2 TTL generator, now instrument plugins are better compatible with Ardour DAW.</li>
<li>Updated french translation (contributed by wargreen at Github and Olivier Humbert).</li>
<li>Migrated hyperlinks from HTTP to HTTPS protocol (contributed by Bruno Vernay).</li>
<li>Added support of build for 32-bit and 64-bit RISC-V architecture (contributed by Xeonacid at GitHub).</li>
<li>Fixed window issue for the Fluxbox window manager.</li>
<li>Fixed build for ARMv8 architecture (contributed by Marek Szuba).</li>
</ul>


<h3>2021-04-01</h3>
<p>LSP Plugins 1.1.30 released!</p>
<ul>
<li><b>Implemented Oscilloscope plugin series: x1, x2 and x4</b>. UX design by Boris Gotsulenko.</li>
<li>Added data streaming port support to plugin framework.</li>
<li>Added strobe feature to mesh primitives that allows to draw multiple streamed meshes together.</li>
<li>Implemented 4-lobe Lanczos oversampling DSP functions for i586 architecture.</li>
<li>Implemented 4-lobe Lanczos oversampling DSP functions for x86_64 architecture.</li>
<li>Implemented 4-lobe Lanczos oversampling DSP functions for 32-bit ARM architecture.</li>
<li>Implemented 4-lobe Lanczos oversampling DSP functions for 64-bit ARM architecture.</li>
<li>Minor bugfixes in the core library.</li>
<li>Fixed bug with character set encoding for several systems with limited iconv.</li>
<li>Fixed latency compensation issue that happened for the 'Bypass' switch/automation.</li>
<li>Implemented additional 'Boosting' mode for the single-band compressor plugin series.</li>
<li>Implemented additional 'Boosting' mode for the multiband-band compressor plugin series.</li>
<li>Updated french translations (contributed by Olivier Humbert).</li>
<li>Updated italian translations by Stefano Tronci.</li>
<li>Desktop icon installation moved to a separate 'install_xdg' icon to prevent LSP
  icon flooding for several systems which don't support XDG standard.</li>
</ul>

<h3>2021-01-19</h3>
<p>LSP Plugins 1.1.29 released!</p>
<ul>
<li>Fixed latency computation error for FIR and FFT modes of the filter.</li>
<li>Optimize FIR and FFT equalizers to use fastconv_ routines instead of FFT.</li>
<li>Implemented SPM mode for Parameteric and Graphic Equalizer plugin series.</li>
<li>Fixed improper VST call handling for several calls.</li>
<li>Several typo fixes in documentation (contributed by Olivier Humbert).</li>
<li>Fixed improper output MIDI event sorting for all plugins.</li>
<li>Channels of spectrum analyzer are now synchronized.</li>
<li>Fixed regression related to loading built-in file resources.</li>
<li>Added extra button to hide/show equalizer section for Impulse Responses and Impulse Reverb plugin series.</li>
<li>Updated OBJ file parsing for support less strict file format.</li>
<li>Fixed improper behaviour of File Open Dialog widget related to file name input.</li>
<li>Art Delay plugin settings changed: by default multipliers are now set to 1, BPM multiplier allows now to step with 0.5 values allowing to set dotted notes.</li>
</ul>

<h3>2020-12-21</h3>
<p>LSP Plugins 1.1.28 available!</p>
<p>Today we celebrate 5 year since the 1.0.0 release. That's hard to imagine but the huge amount of work has been done.</p>
<p>For these 5 years LSP Plugins became one of the top audio plugins used in Linux music production. Among this period new requirements
came to the project and we tried to fulfill them all while caring for the quality, stability and performance of the software.</p>
<p>Anyway, there are some requirements that don't fit into the project and require th codebase to be rewritten. This year was
dedicated for refactoring and decomposition of core modules but, sadly, the refactoring process did not end up to the end of this year.
Hope we'll finally end up with the 1.2.0 release in the next year which will refresh the internal plugin architecture and change user and developer experience.</p>
<p>Merry Christmas and Happy New Year!</p>

<p>The overall list of changes</p>
<ul>
<li><b>Implemented Artistic Delay plugin.</b></li>
<li>Added delay time and phase inversion for each band of the crossover plugin.</li>
<li>MIDI octave numbering now starts with "-1", previously it started with "-2".</li>
<li>Added automatic scanning of installed Hydrogen drumkits to the Multisampler plugin series and possibility to load them by single click in the main menu.</li>
<li>Added sample reversal feature to the Sampler/Multisampler/Trigger plugin series.</li>
<li>Added possibility to change the note number in Sampler/Multisampler/Trigger plugin series by scrolling mouse wheel over the note number indicator or performing mouse double click on it.</li>
<li>Added possibility to use relative paths in file configurations.</li>
<li>Added 'Plugin Manual' menu item to show the documentation to plugin in the browser.</li>
<li>Added 'UI Manual' menu item to show the documentation to UI in the browser.</li>
<li>Added tutorial of using hydrogen drumkits in the Multisampler plugin series - contributed by chrisanthropic @ github.</li>
<li>Updated french translations - contributed by Olivier Humbert.</li>
<li>Plugins now can be built with Clang C++ compiler version 10+.</li>
<li>Refactored ipc::Process for better work with vfork() system call.</li>
<li>Fixed bug in improper RGB -&gt; HSL conversion for UI.</li>
<li>Fixed regression in led indication for the Compensation Delay plugin series.</li>
<li>Fixed drag&drop regression after code refactoring.</li>
<li>Fixed bug in Fraction widget related to nominator drop-down list values.</li>
</ul>

<h3>2020-09-16</h3>
<p>New version of LSP Plugins 1.1.26 available!</p>

<ul>
<li>Implemented Crossover Plugin series.</li>
<li>Fixed phase mismatch between channels in the oversampling mode of limiter (contributed by Hector Martin).</li>
<li>Fixed bug in convolver module which could perform improper tail convolution (reported by Robin Gareus).
    Affected plugins: Impulse Responses, Impulse Reverb, Room Builder.</li>
<li>Fixed small memory leakage in multiband plugins (Compressor, Gate, Expander) related to usage of Inline Display feature.</li>
<li>Added support of pg:mainInput and pg:mainOutput LV2 properties in TTL files for all plugins.</li>
<li>Updated all C++ heades and source files to match the LGPL3+ license headers.</li>
</ul>

<h3>2020-07-16</h3>

<p>LSP Plugins 1.1.24 released!</p>
<p>New release of LSP Plugins is available with the following changes:</p>

<ul>
<li><b>Implemented Loudness Compensator plugin series (Mono and Stereo).</b></li>
<li><b>Implemented Surge Filter plugin series (Mono and Stereo) for protecting audio chain from possible pops on playback start/stop events.</b></li>
<li>Significant changes the Limiter Plugin series, may be partially incompatible with hosts/wrappers:</li>
<ul>
    <li>Removed 'Classic' and 'Mixed' modes since these modes do not give effective results.</li>
    <li>Introduced Automatic Level Regulation (ALR) feature enabled by default.</li>
</ul>
<li>Added possibility to dump internal state of plugin to file.</li>
<li>Several plugins now support dumping of internal state.</li>
<li>Added support of loading Hydrogen drumkits by the Multisampler plugin series.</li>
<li>Added 'Squared Cosine' and 'Cubic Hermite Spline' windows to Spectrum Analyzer.</li>
<li>Fixed bug that caused Spectrum Analyzer to ignore window selection.</li>
<li>Fixed bug in AVX-optimized sidechaining funcion that could cause invalid behaviour of plugins that use external/internal sidechain for processing.</li>
<li>Implemented back-buffering of the window surface, all UI controls now don't glitch on edit.</li>
<li>Refactored and simplified LV2 parameter transport between UI and plugin code.</li>
<li>VST plugins now provide possibility to save and load presets and do not crash Host.</li>
<li>Added support of lv2:StateChanged extension which properly works with Ardour 6.0.145 and higher.</li>
<li>Fixed improper behaviour of VST controls under REAPER host (and possible other VST hosts).</li>
<li>Updated metadata for dynamic processors to better match the UI.</li>
<li>Now all lv2:Atom ports that do not provide MIDI message transfer are marked as lv2:connectionOptional.</li>
<li>Exported plugin configuration now contains information about original package version.</li>
</ul>

<h3>2020-05-31</h3>

<p>LSP Plugins 1.1.22 released!</p>

<ul>
<li>Implemented Multiband Gate plugin series.</li>
<li>Added sidechain low-pass and high-pass filters for Compressor plugin series.</li>
<li>Added sidechain low-pass and high-pass filters for Expander plugin series.</li>
<li>Added sidechain low-pass and high-pass filters for Gate plugin series.</li>
<li>Added sidechain low-pass and high-pass filters for Dynamic Processor plugin series.</li>
<li>Added sidechain low-pass and high-pass filters for Trigger plugin series.</li>
<li>Fixed VST2 identifiers for Multiband Expander plugin series.</li>
<li>Fixed graph issues related to dot editing.</li>
<li>Added spanish translation of the UI (contributed by Ignotus - ignotus666 at github.com).</li>
<li>Compressor, Expander, Gate and Dynamic Processor plugins now report latency for the lookahead knob.</li>
<li>Fixed mapping of some numpad keys that could cause problems when entering manual value in the UI.</li>
<li>VERSION build variable replaced with LSP_VERSION build variable (contributed by Bruno Vernay).</li>
</ul>

<h3>2020-05-17</h3>

<p>LSP Plugins are currently moving towards 1.2.0 release but that requires huge core updates which are now in progress. This is basically set of minor fixes to the current source tree.</p>
<ul>
<li>Fixed UI synchronization issue on plugin state restore for VST plugin format.</li>
<li>Fixed improper work of default Attack and Release time parameters for Dynamic Processor plugin series.</li>
<li>Fixed build broken by recent LV2 header updates.</li>
<li>Some french translation fixes (contributed by Olivier Humbert).</li>
</ul>

<h3>2020-04-19</h3>

<p>This is mainly the patch set to the latest 1.1.17 release.</p>

<p>List of changes:</p>
<ul>
<li>Updated XDG desktop application integration.</li>
<li>Refactoring of sse::fft functions for better portability between different compilation options.</li>
<li>Fixed bug in asimd::hdotp functions for AArch64 that caused invalid result output.</li>
<li>LV2 UI and standalone JACK plugins are now resizable.</li>
<li>Fixed plugin sizing issues on certain new Linux distributions.</li>
<li>Refactoring and several fixes of decoding and encoding of MIDI protocol messages.</li>
<li>Fixed silent MIDI output for JACK plugins.</li>
<li>Profile binaries are now excluded from release build.</li>
</ul>

<h3>2020-04-05</h3>

<p>LSP Plugins 1.1.17 - set of fixes.</p>

<p>This release solves some additional problems found since 1.1.14 and 1.1.15 release.
The full list of changes:</p>
<ul>
<li>Desktop integration icons are now more properly following the XDG standard.</li>
<li>Fixed Lanczos resampling kernel formula for oversampling operations.</li>
<li>Fixed improper display of units in the UI.</li>
<li>Added French translation of the UI (contributed by Olivier Humbert).</li>
<li>Added Italian translation of the UI (contributed by Stefano Tronci).</li>
<li>Fixed non-working right sidechain channel for stereo version of
  Compressor, Dynamic Processor, Expander and Gate plugins.</li>
<li>Added WM_NAME, WM_CLASS and WM_WINDOW_NAME support for the X11 protocol.</li>
</ul>

<h3>2020-03-29</h3>

<p>LSP Plugins 1.1.15 is out!</p>

<p>This release mostly fixes problems found in the 1.1.14 release.</p>

<p>The overall list of changes:</p>
<ul>
<li>Fixed improper TTL file generation for several plugins.</li>
<li>Restored missing SVG files, implemented proper icon installation.</li>
<li>Added better desktop entry categorization (contributed by Dennis Braun).</li>
<li>Added protection from NaNs, Inf's and denormal floating point values passed to plugins' audio
  inputs, all considered to be invalid values are replaced with zeros.</li>
<li>Added '-fvisibility=hidden' compilation option to avoid conflict between builtin resources of
  shared objects that contain implementation of different plugin formats.</li>
</ul>

<h3>2020-03-21</h3>

<p>LSP Plugins 1.1.14 is out!</p>

<ul>
<li>Implemented Multiband expander plugin series.</li>
<li>Additional DSP optimizations of code with AVX, AVX2 and FMA3 instruction set or i586 and x86_64 architectures.</li>
<li>Additional DSP optimizations for AArch64 architecture.</li>
<li>Reworked and additionally optimized structure of all filter chains.</li>
<li>Refactored and optimized dynamic filter processing.</li>
<li>Updated behaviour of limiter in classic mode.</li>
<li>Implemented multilingual interface support.</li>
<li>Implemented workaround for Drag&Drop support initiated by GTK-based applications.</li>
<li>Added russian translations of the user interface.</li>
<li>Added desktop menu for standalone JACK plugins (contributed by David Runge).</li>
<li>Added scalable vector graphics (SVG) logo (contributed by <a href="http://tkach.de/">Sergey Tkach</a>).</li>
<li>Added 4.5 dB/octave envelope compensation for spectrum analyzer, renamed purple noise to violet noise in UI.</li>
<li>Bypass button now supports LV2:enabled designation, that makes plugin to work more smooth with the host.</li>
<li>Bypass button now handles effSetBypass VST event, that makes plugin to work more smooth with the host.</li>
<li>Now LV2 and VST plugins can be compiled without the UI support.</li>
<li>Removed strict requirement to build UI for LADSPA version of plugins.</li>
<li>Implemented support of LV2 state:mapPath extension.</li>
<li>Denied use of -ffast-math compiler option since it may provide incorrect behaviour of standard C
  library functions like isnan() and isinf().</li>
<li>Fixed upward compressor behaviour: now upward compressor has additional compensation knee that prevents from
  infinite gain boost on low-level input signals.</li>
<li>Fixed bug in trigger that caused notes to be immediately cancelled.</li>
<li>Fixed bug in Profiler plugin that caused crash on saving files to WAV format.</li>
<li>Fixed bug with producing NaNs by dynamic processors in some cases when knee has zero length.</li>
<li>Fixed stack corruption bug when working with UI styles and colors.</li>
<li>Fixed problem with matched Z transform caused by filter characteristics optimizations.</li>
<li>Fixed bug with pop-up menus in parameter's editing popup window that caused user interface to lock.</li>
<li>Added SIGPIPE signal blocking for JACK plugin format at startup.</li>
</ul>

<h3>2019-12-23</h3>

<p>LSP Plugins 1.1.13 - hotfix release.</p>

<p>This is mostly hotfix release aimed to fix problems on processors that provide AVX instruction set but do not provide AVX2 instruction set. Short list of changes:</p>
<ul>
<li>Fixed improper utilization of AVX2 instruction when the CPU provides only AVX instruction set.</li>
<li>Fixed some unit tests.</li>
<li>Removed strict RPATH dependency in build scripts.</li>
</ul>

<h3>2019-12-21</h3>
<p>LSP Plugins 1.1.11 is ready!</p>

<p>This release is published exactly 4 years after the 1.0.0 release of LSP Plugins and is aimed to close many core
and UI technical debts. All these changes make LSP Project much better in the UI experience and much faster from the DSP perspective.</p>
<p>That's because support of additional features like drag&drop, bookmarks and some other neat features were added to the graphical toolkit.</p>
<p>From the other side, low-level DSP code is additionally optimized with AVX and AVX2 instruction sets which allow to gain additional performance
benefits on processors which have fast AVX implementation (All Intel Core 6 generation processors and above, AMD Zen generation processors and above).
There was improved support of AArch64 architecture, and some part of DSP code already has been ported to this architecture. The DSP code for ARMv7
architecture also has been additionally refactored and optimized.</p>
<p>Also, the project became more portable because introduced it's own support of XML document parsing mechanism and does not require the expat library more.</p>
<p>The overall list of changes is the following:</p>
<ul>
<li>Source code now compiles for the ARMv6A architecture.</li>
<li>Implemented incoming drag & drop events support for sample loading and file loading widgets.</li>
<li>Added possibility to double-click the parameter's value and enter it manually with keyboard.</li>
<li>Added bookmark support by file opening/saving dialogs. Bookmarks are also automatically imported from GNOME/KDE desktop environments' configuration files.</li>
<li>Refactored UI of the Parameteric Equalizer plugin series.</li>
<li>Added allpass filters to Parametric Equalizer plugin.</li>
<li>Added knobs that allow to simultaneously shift frequency for all active filters in the Parametric Equalizer plugin series.</li>
<li>Added support of RoomEQ Wizard configuration file format import by Parametric Equalizer plugin series.</li>
<li>Refactored UI of the Graphic Equalizer plugin series.</li>
<li>Updated UI of the Multiband Compressor plugin series.</li>
<li>Implemented allpass filters that add phase compensation for the classic mode of the Multiband compressor and allow to achieve flat frequency response.</li>
<li>Changed reverb simulation algorithm for the Room Builder plugin: the algorithm now handles capture objects as opaque objects, not part of the 3D scene. So the number and location of the captures does not impact the audio simulations. This allows to render the impulse response in more accurate way. However, even after some set of additional optimizations has been made, this yields to some performance degradation because there are much more ray groups required to be processed.</li>
<li>Added possibility to export state to clipboard and import state from clipboard from the UI of any plugin.</li>
<li>Added possibility to freeze all graphs in the Spectrum Analyzer simultaneously.</li>
<li>Added MIDI groups to Multisampler plugin series.</li>
<li>Added possibility to control how panning and gain controls affect the signal passed to the direct output tracks of the Multisampler plugin.</li>
<li>Implemented JSON and JSON5 parsing and serializing mechanisms.</li>
<li>Implemented XML parsing mechanism without built-in DOCTYPE definition support.</li>
<li>Removed EXPAT library from build dependencies since LSP Plugins have their own XML parser.</li>
<li>Implemented more advanced expression language for UIs.</li>
<li>Implemented styling system and basic styling mechanism for UIs.</li>
<li>Additionally optimized DSP biquad filters for 32-bit ARM NEON instruction set.</li>
<li>Additional DSP optimizations for AArch64 architecture.</li>
<li>Additional DSP optimizations of code with AVX, AVX2 and FMA3 instruction set for i586 and x86_64 architectures.</li>
<li>Refactored clipboard mechanism for X11 protocol, addes support of INCR selection transfer protocol.</li>
<li>Refactoring of the UI widgets, eliminated old UI code and some deprecated facilities from all widgets.</li>
<li>Implemented basic styling mechanism for UI widgets.</li>
<li>Implemented ipc::Process class that allows to run nested processes with I/O redirection.</li>
<li>Code now prefers vfork() system call agains fork().</li>
<li>Fixed memory corruption bug in Analyzer core module that could crash the system on non-power-of-two buffer sizes. Affected plugins: Parametric Equalizer, Graphic Equalizer, Spectrum Analyzer, Multiband Compressor.</li>
<li>Fixed GLX context synchronization issues that could lead to crashes on several systems.</li>
<li>Fixed bug with improper mouse pointer coordinates for nested menus.</li>
<li>Fixed bug with improper latency value reported by Limiter plugin series.</li>
<li>Fixed corrupted LADSPA binaries due to lack of objects that contain non-required built-in resources. Replaced objects with empty stubs.</li>
<li>Fixed problem of improper loading of 3D Wavefont OBJ files that was caused by improper texture coordinate handling.</li>
<li>Build fixes for AArch64 architecture related to CPU feature detection.</li>
<li>Updated VST state serialization mechanism to version 3: now plugin properly handle state if there</li>
<li>is no chunk header in the chunk data passed from the host.</li>
</ul>

<h3>2019-07-23</h3>
<p>LSP Plugins release 1.1.10 is ready!</p>
<p>The development of 1.1.10 version took the longest cycle in contrast to previous releases.</p>
<p>All the changes were planned since January 2019 but many supplementary problems should be solved first.</p>
<p>This release contains a lot of internal plugin framework changes, implementation of new UI widgets and,
probably, a killer toy: LSP Room Builder plugin series. This plugin allows to simulate impulse response of ANY
room (and not only room). You just need to model it first in a 3D editor/designer and export as a Wavefont (OBJ) file.</p>
<p>The overall changelist is the following:</p>
<ul>
<li><b>Implemented 3D reverb simulator plugin series - Room Builder Mono and Room Builder Stereo</b>.</li>
<li>Fixed improper InlineDisplay feature support in LV2 TTL files.</li>
<li>Plugin names now have 'LSP' prefix for the LV2 format.</li>
<li>Each plugin can now provide it's custom UI class which is derived from common plugin's UI class.</li>
<li>Code now compiles for the PPC64 architecture.</li>
<li>Code now compiles for the IBM s390x architecture.</li>
<li>Added command-line option to pass configuration file name for standalone JACK plugins.</li>
<li>The settings stored in configuration file will be automatically loaded at startup.</li>
<li>Updated function signatures for libraries which now support '-fvisibility=hidden' compilation flag.</li>
<li>Added support of nested menus in the UI toolkit.</li>
<li>Added 3D scene rendering support in the UI by using different rendering backends.</li>
<li>Implemented GLX backend based on openGL 2.x for rendering 3D scenes.</li>
<li>Implemented key-value tree storage (KVTStorage) for storing and managing dynamic parameters.</li>
<li>Implemented key-value tree storage (KVTStorage) UI&lt;-&gt;DSP synchronization mechanisms.
<li>Implemented key-value tree storage (KVTStorage) serialization/deserialization in parameters.</li>
<li>Implemented key-value tree storage (KVTStorage) serialization/deserialization for the plugin state.</li>
<li>Implemented mechanism for UI<->DSP OSC message interchange.</li>
<li>Added support of OSC protocol messages serialization, deserialization and pattern matching.</li>
<li>Optimized complex number functions for AArch64 architecture.</li>
<li>Changed installation path for jack core library from &lt;lib-path&gt; to &lt;lib-path&gt;/lsp-plugins.</li>
<li>Added more careful file type analysis for non-EXT file systems.</li>
<li>Added support of Hygon Dhyana x86 family CPU optimizations and detection of some other CPU vendors.</li>
<li>Now both release and test binaries are available to build into separate subdirectories independently.</li>
<li>Improved built-in resource generation tool.</li>
<li>Embedded resources are now alphabetically sorted to make the build more deterministic.</li>
<li>Added support of built-in presets for plugin's UI.</li>
</ul>

<h3>2019-03-23</h3>
<p>This is mostly a hot-fix release for regressions occurred in a 1.1.7 release. Anyway, there are also some couple of new changes:</p>
<ul>
<li>Added experimental support of AArch64 architecture (DSP code is not optimized yet).</li>
<li>Fixed regression in VST and Standalone plugin formats that caused offline tasks to not to be launched.</li>
<li>Improved build system to make source code possible to build for KXStudio repository. Distribution builders should ensure that the GNU C++ compiler is selected at the build stage.</li>
<li>Additional I/O improvements for better WindowsNT support.</li>
</ul>
<p>Also, you may now observe LSP Plugins in <a href="https://kx.studio/">KXStudio</a> repositories as a <b>lsp-plugins</b> package.</p>

<h3>2019-03-17</h3>
<p>We care about the quality of provided plugin bundle and release new bug-fix release that contains the following changes:</p>
<ul>
<li>Implemented fully compatible with EqualizerAPO software digital filters that are now part of the Parametric Equalizer plugin series.</li>
<li>Fixed issue that caused VST plugins not to load from cusom user-defined path.</li>
<li>Some code parts rewritten for better compatibility with WindowsNT platform.</li>
<li>Fixed file listing in the file dialog that caused improper reading of remote directories mounted on the local file system.</li>
<li>Changed implementation of LV2 MIDI transport so now plugins utilize only one LV2:Atom input port and one LV2:Atom output port.</li>
<li>Fixed bug in host<->UI time and position synchronization for VST plugin format.</li>
<li>Fixed AVX+FMA3 implementation of dynamic biquadratic filters that caused improper behaviour of the Multi-band compressor plugin on machines that support AVX and FMA3 instruction set.</li>
<li>Multiband compressor now properly handles the 'Bypass' button.</li>
<li>Fixed return of improper extension pointers when requested extension is different to the ui:idleInterface.</li>
<li>Fixed bug in dynamic processor plugin that could issue invalid metering values.</li>
<li>Fixed UI size issue for VST plugins in Cockos Reaper.</li>
<li>Fixed numerous memory leakage issues in UI components.</li>
<li>Fixed spontaneous crashes when destroying the Profiler Mono/Stereo plugin.</li>
<li>Fixed embedded resource generation tool that had invalid behaviour for several file systems.</li>
<li>Reimplemented I/O subsystem.</li>
</ul>

<h3>2018-12-21</h3>
<p>We publish new 1.1.5 release with many improvements exactly three years after the 1.0.0 release!</p>
<p>Merry Christmas and Happy new Year!</p>
<ul>
<li><b>Implemented stereo version of Profiler plugin.</b></li>
<li>Added 'Spectralizer' and 'Mastering' modes to the Spectrum Analyzer plugin series.</li>
<li>All SIMD-optimized DSP code now ported to ARMv7A architecture and optimized using ARM NEON instruction set.</li>
<li>Added Frame Buffer primitive support by plugins and widgets.</li>
<li>Implemented RGBA and HSLA color manipulation routines for point array rendering optimizations.</li>
<li>Extended unit and performance test coverage.</li>
<li>Enabled RELRO and PIE option for binaries, simplified build system.</li>
<li>Implemented optimized DSP functions for minimum and maximum search.</li>
<li>Implemented optimized DSP functions for static biquad processing, dynamic biquad processing, dynamic bilinear transformation.</li>
<li>Extended DSP code with different set of software rendering functions that enhance visual effects.</li>
<li>Added support of FreeBSD operating system (plugins are available for building in FreeBSD ports).</li>
<li>Improved build process, added possibility to specify PREFIX variable for installing into specified path instead of /usr/local.</li>
<li>Fixed building issues under Ubuntu Linux related to compiler and linker flags reordering.</li>
<li>Fixed system character set detection on certain systems that caused disappearing of text labels in the UI.</li>
<li>Fixed window decorating issue under the i3 window manager.</li>
<li>Fixed biquad filter processing routines that could cause memory corruption and/or invalid behaviour in certain circumstances.</li>
<li>Fixed serious memory corruption in SSE implementation of fast convolution routines that could cause spontaneous crashes of convolvers.</li>
<li>Fixed buffer underflow in Convolver module that could cause memory corruption and spontaneous crashes of host.</li>
</ul>

<h3>2018-09-29</h3>
<p>Release 1.1.4 is coming out with a lot of new changes!<p>
<p>First of all, LSP Plugins became completely open source and are licensed under terms of GNU LGPL v3 license!</p>
<p>Additionally, experimental support of ARMv7-A architecture added, basically for Raspberry Pi 3B/3B+ devices.</p>
<p>The overall list of changes is listed below:</p>
<ul>
<li><b>Changed licensing to GNU Lesser General Public License version 3 (GNU LGPL v3).</b></li>
<li><b>Moved code repository to GitHub while keeping release history.</b></li>
<li><b>Implemented linear impulse response profiler plugin.</b></li>
<li><b>Added basic Raspberry Pi 3B/3B+ (ARMv7A) support (experimental).</b></li>
<li>Implemented unit testing subsystem.</li>
<li>Implemented performance testing subsystem.</li>
<li>Implemented manual testing subsystem.</li>
<li>Fixed and optimized convolution algorithm for convolver module that produced invalid output.</li>
<li>Added LSPC file format implementation.</li>
<li>Added LSPC file format support to convolver plugins.</li>
<li>Huge refactoring: DSP code moved from core to separate subtree.</li>
<li>Partially implemented NEON SIMD instruction support for some DSP assembly functions for ARMv7A architecture.</li>
<li>Fixed bugs in some DSP oversampling routines.</li>
<li>Optimized complex multiplication functions.</li>
<li>Implemented additional complex number routines.</li>
<li>Implemented additional functions to DSP core.</li>
<li>Fixed compilation warnings and errors emitted by the GCC 8 compiler.</li>
<li>Updated development documentation.</li>
</ul>

<h3>2018-07-05</h3>
<p>New release with many improvements is ready!</p>
<ul>
<li>Updated File saving widget to support different kinds of file types.</li>
<li>Added support of latency report by JACK version of plugins.</li>
<li>Added support of playback position report (BPM, etc) by JACK transport for JACK plugin format.</li>
<li>Added support of playback position report (BPM, etc) by host for LV2 plugin format.</li>
<li>Added support of playback position report (BPM, etc) by host for VST plugin format.</li>
<li>Added emulation of playback position report for LADSPA wrapper.</li>
<li>Implemented Fraction widget for editing time signature.</li>
<li>Implemented Tempo tap widget for manually adjusting tempo.</li>
<li>Added possibility to configure Slap-Back delay plugin series using BPM-related time units.</li>
<li>All grid lines are made more 'darken' in the UI.</li>
<li>Added delay ramping (interpolation) option for Compensation Delay plugin series. This feature
  allows to apply soft delay change when applying automation in DAW.</li>
<li>Added delay ramping (interpolation) option for Slap-Back Delay plugin series. This feature
  allows to apply soft delay change when applying automation in DAW.</li>
<li>Added modules for reading text files.</li>
<li>Re-implemented parameter serializing and deserializing interface for more flexible and safe usage.</li>
<li>Added possibility to copy samples and impulse response files between different AudioFile widgets.</li>
<li>Fixed GUI crash when pasting data from clipboard.</li>
<li>Added ability to move the split bars on the Multiband Compressor's graph with the mouse.</li>
</ul>

<h3>2018-05-01</h3>
<p>After hard work, new release of LSP Plugins, version 1.1.2, is ready!</b>
<p>The main list of changes is following:</p>
<ul>
<li><b>Implemented Muliband Compressor plugin series.</b></li>
<li>Added possibility to mute playback by triggering the 'note off' MIDI event in sampler plugin series.</li>
<li>Implemented ComboGroup wiget for dynamically switching internal contents of the group.</li>
<li>Fixed error in filter processing algorithms that could yield to invalid
  results when source and destination buffers differ.</li>
<li>Additionally tuned SSE code of static filters.</li>
<li>Refactored CPU identification routines and optimized assembly routines selection.</li>
<li>Small fixes in UI grid cell allocation and rendering.</li>
<li>Improved design of markers: added gradient highliting.</li>
<li>Fixed some UI issues related to switched port values.</li>
<li>Removed support of some plugins by LADSPA that can not be supported by LADSPA.</li>
<li>Optimized calculations related to logarithmic axis.</li>
<li>Fixed bug in UI expression language that incorrectly interpreted 'not' operation.</li>
<li>Added power mathematical operator to UI expression language.</li>
</ul>

<h3>2018-01-27</h3>
<p>LSP Plugins release 1.1.1 available. Some bugs related to the new UI were fixed.</p>
<p>Also some improvements of already existing plugins were done. Here is the complete list of changes:</p>
<ul>
<li><b>Plugins have been tested under QTractor 0.9.5 and it seems that they are working well.</b></li>
<li>Fixed UI resize issue in Carla host.</li>
<li>Fixed UI redraw issue that didn't show widgets for several cases.</li>
<li>Optimized spectrum analysis modules by changing code so it now uses more cache-friendly FFT routines.</li>
<li>Added graph amplitude scaling to Graphic and Parametric equalizers.</li>
<li>Added graph amplitude scaling to Spectrum Analyzer plugin.</li>
<li>Fixed possible memory corruption while redrawing the inline display image of parametric equalizer.</li>
<li>Fixed possible memory corruption in the Dynamic Processor plugin.</li>
<li>Fixed latency report for equalizer plugins.</li>
<li>Updated wrapper to make plugins properly supported by Bitwig Studio that doesn't know anything about
  kVstParameterUsesIntStep and kVstParameterUsesIntegerMinMax flags.</li>
<li>Refactored event handling mechanism for widgets: added 'sender' parameter to indicate the widget that
  initiated slot execution.</li>
<li>UI now stores last used path when loading samples, impulse response files and configuration files in
  the global configuration file.</li>
<li>Fixed problem with UI update in cases when window size does not change. Now works properly.</li>
<li>Added Mid/Side adjustment knobs for Mid/Side versions of equalizer plugins.</li>
<li>Reorganized core modules into more comfortable source code tree.</li>
<li>Added support of UI:updateRate parameter reported by the LV2 host to the UI.</li>
<li>Added multiple-channel (up to 8 channels) audio file support to Impulse Reverb plugin.</li>
<li>Updated documentation related to the Limiter plugin.</li>
<li>Added possibility to equalize the processed signal in convolution plugins.</li>
<li>JACK version of plugins now automatically handles JACK startup/shutdown and does not require to restart plugin.</li>
</ul>

<h3>2018-01-01</h3>
<p>Happy New 2018 Year!</p>
<p>Accurately to this date we've gathered donations for new <b>Source Code Release - SCR 1.0.4</b>!<p>
<p>The changelog is, as usual, simple:<p>
<ul>
<li>Published source code for the LSP Spektrumanalysator - Spectrum analyzer plugin.</li>
</ul>


<h3>2017-12-21</h3>

<p>After a long delay, the new <b>1.1.0</b> release hase been made!</p>
<p>Today we also celebrate the second year of project lifetime since the 1.0.0 release!</p>
<p>The release slogan can be pronounced as: &quot;Farewall to GTK!&quot;, that's why this release includes a lot of HUGE UI changes, so please try it carefully before using on your projects!</p>
<p>The overall changelist is the following:</p>
<ul>
<li><b>Ported all widgets from GTK+ 2.x to raw X11 + cairo</b></li>
<li><b>Ardour DAW is supported by the UI as before</b></li>
<li><b>Mixbus DAW is supported by the UI as before</b></li>
<li><b>JUCE-based hosts are now supported by UI</b></li>
<li><b>Tracktion DAWs are now supported by UI</b></li>
<li><b>Renoise DAW is now supported by UI</b></li>
<li><b>Bitwig Studio DAW is now supported by UI</b></li>
<li><b>REAPER native linux version is now supported by UI</b></li>
<li>Updated JACK plugin wrapper to support new UIs</li>
<li>Updated VST pluign wrapper to support new UIs</li>
<li>Updated LV2 plugin wrapper to support new UIs</li>
<li>Implemented LV2:Instance support feature for optimizing LV2 DSP &lt;-&gt; UI transfers</li>
<li>Official Steinberg VST 2.4 SDK is not required more for building VST plugins</li>
<li>Added version check for JACK core libraries to prevent multiple installations conflict</li>
<li>Requirements of naming JACK core library were reduced to only contain 'lsp-plugins' substring</li>
<li>Added version check for VST core libraries to prevent multiple installations conflict</li>
<li>Requirements of naming VST core library were reduced to only contain 'lsp-plugins' substring</li>
<li>Ported Gtk2Box widget to X11UI widgets</li>
<li>Ported Gtk2Button widget to X11UI widgets</li>
<li>Ported Gtk2Cell widget to X11UI widgets</li>
<li>Ported Gtk2Grid widget to X11UI widgets</li>
<li>Ported Gtk2Indicator widget to X11UI widgets</li>
<li>Ported Gtk2Label widget to X11UI widgets</li>
<li>Ported Gtk2Led widget to X11UI widgets</li>
<li>Ported Gtk2Separator widget to X11UI widgets</li>
<li>Ported Gtk2Switch widget to X11UI widgets</li>
<li>Ported Gtk2Knob widget to X11UI widgets</li>
<li>Ported Gtk2Meter widget to X11UI widgets</li>
<li>Ported Gtk2Group widget to X11UI widgets</li>
<li>Ported Gtk2Align widget to X11UI widgets</li>
<li>Ported Center widget to X11UI widgets</li>
<li>Ported Axis widget to X11UI widgets</li>
<li>Ported Marker widget to X11UI widgets</li>
<li>Ported Basis widget to X11UI widgets</li>
<li>Ported PortAlias widget to X11UI widgets</li>
<li>Ported Text widget to X11UI widgets</li>
<li>Ported Mesh widget to X11UI widgets</li>
<li>Ported Dot widget to X11UI widgets</li>
<li>Ported IGraph widget to X11UI widgets</li>
<li>Ported Gtk2Graph widget to X11UI widgets</li>
<li>Ported Gtk2ComboBox widget to X11UI widgets</li>
<li>Ported Gtk2Window widget to X11UI widgets</li>
<li>Ported Gtk2File widget to X11UI widgets</li>
<li>Ported Gtk2Body widget to X11UI widgets</li>
<li>Ported Gtk2MountStud widget to X11UI widgets</li>
<li>Implemented ScrollBar widget</li>
<li>Implemented Edit widget</li>
<li>Implemented ListBox widget</li>
<li>Implemented Menu widget</li>
<li>Implemented File Save/Open dialog</li>
<li>Implemented Hyperlink widget</li>
<li>Implemented Fader widget </li>
<li>Implemented File saving widget</li>
<li>Implemented basic clipboard support</li>
<li>Code clean up and project tree refactoring</li>
<li>Fixed inline display drawing issue related to GCC 6 optimization specifics (thanks to Robin Gareus)</li>
<li>Changed maximum sample length of the Schlagzeug plugin up to 64 seconds</li>
<li>Changed maximum sample length of the Triggersensor plugin up to 64 seconds</li>
</ul>

<h3>2017-07-09</h3>
<p>New release 1.0.26 available. </p>
<ul>
<li><b>Implemented Latenzmessgerät - Latency Meter plugin.</b></li>
<li>Fixed horizontal meter widget rendering.</li>
</ul>

<h3>2017-05-25</h3>
<p>Source Code Release (SCR) Version <b>1.0.2</b> of plugins now available!</p>
<p>The donation goal of $300 for the <b>Phase Detector plugin</b> has been reached some days ago thanks to your donations.</p>
<ul>
  <li>Published source code for the LSP Phasendetektor - Phase detector plugin.</li>
  <li>Updated core modules up to 1.0.24 version.</li>
</ul>
<p>Source code release 1.0.2 is accessible from <a href="https://sourceforge.net/p/lsp-plugins/svn/HEAD/tree/tag/scr-1.0.2/">SVN repository at SourceForge.net</a></p>
<p>You may help the source code of other plugins to become released by <a href="https://salt.bountysource.com/teams/lsp-plugins">donating the project</a>.</p>
<p>More information about donation and policy of source code publishing may be <a href="/?page=download">obtained here</a>.</p>

<h3>2017-05-19</h3>
<p>
  The new release of LSP Plugins is available!
  This release does not contain huge impreovements but we have another good news:
  <b>Stefano Tronci</b> aka <b>Crocoduck</b> has joined to LSP Project team and provided his first plugin!
</p>
<p>The complete set of changes:</p>
<ul>
<li>Implemented Oszillator - Oscillator utility plugin.</li>
<li>Fixed CPUID bug that caused Segfault on 32-bit systems.</li>
<li>Added version to the name of the VST core library to prevent possible conflicts with previous installations.</li>
<li>Added version to the name of the JACK core library to prevent possible conflicts with previous installations.</li>
<li>Updated debugging engine that allows to write trace file into /tmp.</li>
<li>Fixed UI hangup when showing plugin's UI related to gtk_dialog_run() issue.</li>
</ul>

<h3>2017-03-25</h3>

<p>New release 1.0.23 available. Mostly it's a bug-fix release.</p>
<ul>
<li>Fixed buffer overflow in Slap-back Delay plugin series happening with frame size larger than 512 samples.</li>
<li>Updated expression language for the UI: added literal expressions that are more friendly with XML syntax.</li>
<li>DSP core functions re-engineering and DSP core interface refactoring.</li>
<li>Covered many of SSE DSP functions with unit tests.</li>
</ul>


<h3>2017-03-11</h3>
<p>The new 1.0.22 release of LSP Plugins is available. Many optimization work was done relative to realtime
convolution algorithms. Also, as usual, new plugins are implemented. Detailed changelog:</p>
<ul>
<li><b>Implemented slap-back delay plugin series.</b></li>
<li><b>Implemented Impulsnachhall (Impulse Reverb) plugin series as advanced version of Impulsantworten.</b></li>
<li>Implemented FFT routines that work with packed complex numbers. Overall SSE performance is 1.4 times
  higher on AMD processors and about 1.1 higher on Intel processors.</li>
<li>Implemented FFT-based fast convolution routines (Native and SSE) that work with real data input and
  real data output and allow to avoid bit-reversal shuffle of the signal data. Also there are serious
  fixes relative to AMD cache penalties. Overall performance bonus is about 1.5 times to the previous
  convolution implementation.</li>
<li>Added Full-oversampling modes to Limiter plugin.</li>
<li>Updated LV2 atom transport primitives.</li>
<li>Fixed problem in resampler that didn't allow to set oversampling more than 4x.</li>
<li>Fixed filter core that didn't properly update settings of filter in the specific case and broke
  behaviour of oversampler.</li>
<li>Minor UI updates.</li>
</ul>

<h3>2017-01-27</h3>
<p>LSP Plugins package version 1.0.20 is out! There are many changes since previous release.</p>
<ul>
<li><b>Implemented Impulsantworten (Impulse responses) zero-latency high-performance
  convolution plugin series.</b></li>
<li>Added Mixed Herm, Mixed Exp and Mixed Thin modes to Limiter.</li>
<li>Updated Classic mode of Limiter that caused a lot of unpleasant distortion.</li>
<li>Added dithering support to Limiter plugin.</li>
<li>Added 6x and 8x oversampling support to Limiter plugin.</li>
<li>Added lookahead delay line to all dynamic processing plugins (Dynamikprozessor,
  Kompressor, Gate, Expander).</li>
<li>Updated UI of all Spektrumanalysator plugin series. Now it takes less place while
  keeping the same functionality.</li>
<li>Added notification dialog that asks for donations on each version update.</li>
<li>Implemented zero-latency convolver core module.</li>
<li>Implemented dither core module.</li>
<li>Updated delay core module to become more safe while processing passed data to the input.</li>
<li>Optimized native implementation of FFT: sine and cosine calculation replaced by complex
  vector rotating, fixed performance penalties relative to CPU caching issues. </li>
<li>Optimized SSE implementation of FFT: sine and cosine calculation replaced by complex vector
  rotating, fixed performance penalties relative to CPU caching issues. Overall
  performance was raised about 4x times.</li>
<li>All atom ports for LV2 plugins now have twice greater rsz:minimumSize property.</li>
<li>Added workaround for VST plugins that crashed because Ardour didn't properly report
  sample rate to multiple instances.</li>
<li>VST plugins can now be installed as directory with .so files into VST_PATH.</li>
<li>Some minor code refactoring.</li>
</ul>


<h3>2016-12-21</h3>
<p><b>LSP Plugins</b> celebrate one year since first 1.0.0 release! This day, new release of version 1.0.18 has been published.</b>
<ul>
<li><b>Implemented Begrenzer Mono, Stereo, Sidechain Mono, Sidechain Stereo (Limiter) plugin series.</b></li>
<li><b>Added KVRDC16 entry - Dynamikprozessor Mono, Stereo, LeftRight, MidSide plugin series.</b></li>
<li>Added 2x/3x/4x oversampling support by DSP modules.</li>
<li>Small code refactoring.</li>
</ul>

<h3>2016-12-02</h3>
<p>LSP Plugins have joined <a href="https://www.kvraudio.com/kvr-developer-challenge/2016/">KVR Audio Developers Challenge 2016!</a></p>
<p>The official page of competition bundle is available at <a href="http://www.kvraudio.com/product/lsp-dynamikprozessor-plugin-series-by-linux-studio-plugins-project">this link</a></p>
<p>This is special release of bundles and does not contain regular plugin bundles. But this release may be independently set up on your system</p>
<p>We need your votes! Please support bundles by your votes and reviews! All competitors are available <a href="https://www.kvraudio.com/kvr-developer-challenge/2016/#kvrdc2016entries">here</a></p>
<ul>
<li><b>Implemented Dynamikprozessor Mono, Stereo, LeftRight, MidSide plugin series with and without additional sidechain inputs.</b></li>
</ul>


<h3>2016-11-14</h3>
<p>Release 1.0.16 continues the extension of the set of dynamic processors. This release adds two additional bundles - Gate and Expander. Also there are couple of changes to the UI engine. More information below:</p>
<ul>
<li><b>Implemented Expander Mono, Stereo, LeftRight, MidSide plugin series.</b></li>
<li><b>Implemented Gate Mono, Stereo, LeftRight, MidSide plugin series.</b></li>
<li>Added expression language to XML documents that allows to evaluate floating-point values.</li>
<li>Added feature to meter widget: now middle point-relative output is supported.</li>
<li>Added possibility to display two values for one metering widget simultaneously.</li>
<li>Some metadata corrections.</li>
<li>Updated naming of LinuxVST .so files: all underscore characters are replaced by minuses.</li>
<li>Added 'TROUBLESHOOTING' section to README.txt and documentation that describes how to</li>
<li>pefrorm detailed bug/crash report.</li>
<li>Fixed bug of sidechain triggered when it's working in mid-side mode.</li>
<li>Updated UI design of the trigger relative to metering.</li>
</ul>

<h3>2016-10-26</h3>
<p>LSP Plugins bugfix release 1.0.15.</p>
<ul>
<li>Updated metadata to place english names first for all plugins, and german names afterwards.</li>
<li>Updated Triggersensor, Parametrischer Entzerrer, Grafischer Entzerrer, Kompressor plugin series UI:
  changed input and output level meters that now display both peak and RMS values.</li>
<li>Removed JUCE workaround to prevent crashes of Renoise DAW.</li>
</ul>

<h3>2016-10-18</h3>
<p>New release from Linux Studio Plugins Project, version 1.0.14.</p>
<ul>
<li><b>Implemented Kompressor Mono, Stereo, LeftRight, MidSide (Compressor) plugin series</b></li>
<li><b>Implemented Seitenkette Kompressor (Sidechain Compressor) Mono, Stereo, LeftRight, MidSide plugin series</b></li>
<li>Moved sidechain processing functions into independent separate DSP module.</li>
<li>Fixed issue related to LV2 bug that disallows to save preset of plugin if it has square brackets in the name.</li>
<li>Fixed build issue triggering on GCC 6: default C++ standard in GCC was changed to C++11, added strict C++98 usage into makefiles.</li>
<li>Fixed time drifting between Middle and Side channels in FIR/FFT mode for Graphic Equalizer and Parametric Equalizer.</li>
<li>Fixed and updated metadata of Graphic Equalizer plugin: now filters have individual pre-defined frequencies, Graphic Equalizer's tranlation is corrected to 'Graphic Equalizer'.</li>
<li>Re-worked LSP Parametrischer Equalizer plugin series: added filter mode parameter.</li>
<li>A bit improved support of LinuxVST for Tracktion DAW and other JUCE'd plugins. Great thanks to Nick Dowell, the developer of amsynth. Still, there are problems with UI redraw.</li>
<li>Implemented biquad x8 filter bank using SSE, SSE3, AVX and FMA instructions, overall performance goal is about 1.8x on x86_64 architecture, i586 architecture has no performance regressions when switching from two x4 banks to one x8 bank.</li>
<li>Fixed issues with peak value transfer from DSP to UI for VST and JACK versions of plugin.</li>
</ul>

<h3>2016-09-10</h3>
<p>LSP Plugins version 1.0.12 released!</p>
<p>Many optimization work was done that highly increase performance. Additional features are now provided for most plugins. Here is the following list of changes:</p>
<ul>
<li><b>Implemented LSP Grafischer Entzerrer (Graphic Equalizer) x16 Mono/Stereo/LeftRight/MidSide plugin series.</b></li>
<li><b>Implemented LSP Grafischer Entzerrer (Graphic Equalizer) x32 Mono/Stereo/LeftRight/MidSide plugin series.</b></li>
<li><b>Additional package with HTML documentation is now provided.</b></li>
<li>Fixed behaviour of trigger-type button when right mouse click occurs.</li>
<li>Added output balance knob to LSP Parametrischer Entzerrer Stereo/LeftRight/MidSide plugin series.</li>
<li>Implemented multiband crossover processor in DSP core for future use.</li>
<li>Added Inline Display LV2 extension support provided by Ardour to all LV2 plugin series that use interactive graphic output.</li>
<li>Standalone versions of plugins now draw their state on window icons similarly to Inline Display extension.</li>
<li>Optmized processing speed of IIR filters, overall acceleration is about 1.5x for filters with low number of poles and zeros, about 5x for filters with high amount of poles and zeros.</li>
<li>Optimized equalizer structure by using filter banks. Low-pole filters in equalizer are combined into 4x biquad filter banks. Overall performance improvement is about 4x.</li>
<li>Floating-point calculations are more accurate now for FIR filters.</li>
<li>Added output signal metering to LSP Parametrischer Entzerrer plugin series.</li>
<li>Moved spectrum analysis into separate core module for making re-usable.</li>
<li>Fixed bugs of floating-point number formatting by the 'indicator' widget.</li>
<li>Added possibility to minimize visual space used by plugin by reducing size of rack mount studs in the UI.</li>
<li>Plugin UIs now store global configuration in ~/.config/lsp-plugins/lsp-plugins.cfg file.</li>
<li>Fixed MIDI output for JACK wrapper.</li>
<li>Added avoiding of denormal floating point values that may cause extra CPU performance penalty by modifying MXCSR register value before audio processing stage and restoring it's value after audio processing stage.</li>
</ul>

<h3>2016-08-09</h3>
<p>Version <b>1.0.11</b> of plugins has been released.</p>
<ul>
    <li>Optimized graph widget relative to axis objects, logarithmic scaling is now implemented in SSE2 and is about 6 times faster than native implementation.</li>
    <li>Refactored widget rendering subsystem: FPS is lowered and stabilized around 20 FPS.</li>
    <li>Implemented additional compression of built-in XML resources for the UI, now XML resources obtain about 3 times lower space in binaries.</li>
    <li>Highly optimized rendering of Gtk2Graph and Gtk2File widgets that caused excessive CPU load: the CPU utilization is now about 7 times lower.</li>
</ul>

<h3>2016-08-02</h3>
<p>Source Code Release (SCR) Version <b>1.0.0</b> of plugins now available!</p>
<p>The first donation goal of $100 has been reached some days ago thanks to your donations.</p>
<ul>
    <li>Published source code for the LSP Verzögerungsausgleicher - Delay compensator plugin series.</li>
</ul>
<p>Source code is accessible from <a href="https://sourceforge.net/p/lsp-plugins/svn/HEAD/tree/trunk/lsp-plugins/">SVN repository at SourceForge.net</a></p>
<p>You may help the source code of other plugins to become released by <a href="https://salt.bountysource.com/teams/lsp-plugins">donating the project</a>.</p>
<p>More information about donation and policy of source code publishing may be <a href="/?page=download">obtained here</a>.</p>

<h3>2016-07-30</h3>
<p>Version <b>1.0.10</b> of plugins has been released.</p>
<ul>
    <li><b>Implemented Parametrischer Entzerrer (Parametric Equalizer) x16 Mono/Stereo/LeftRight/MidSide plugin series.</b></li>
    <li><b>Implemented Parametrischer Entzerrer (Parametric Equalizer) x32 Mono/Stereo/LeftRight/MidSide plugin series.</b></li>
    <li>Profiling release now available for standalone version of plugins. Requires JACK server.</li>
    <li>Updated formulas for noise envelopes of Spektrumanalysator plugin series.</li>
    <li>Fixed convolution function in DSP that returned zero on small convolutions.</li>
    <li>Fixed bugs in some functions.</li>
    <li>Implemented SSE routines for operations on small vectors of 4 elements.</li>
    <li>Implemented SSE routines for bulk biquad filter processing.</li>
    <li>Updated license text relative to project developers and project maintainers.</li>
    <li>Implemented filter core.</li>
    <li>Implemented equalizer core that supports IIR, FIR and FFT filtering.</li>
</ul>

<h3>2016-06-12</h3>
<p>Version <b>1.0.8</b> of plugins has been introduced.</p>
<ul>
    <li><b>Implemented Triggersensor Mono/Stereo plugin series.</b></li>
    <li><b>Implemented Triggersensor MIDI Mono/Stereo plugin series.</b></li>
    <li>Implemented JACK wrapper, all plugins now have their standalone implementations.</li>
    <li>Updated LV2 transport: now plugins do not transport primitives when there is no UI connected.</li>
    <li>Updated LADSPA wrapper: added latency reporting ports for the plugins.</li>
    <li>Implemented peak transfer protocol for peak values.</li>
    <li>Implemented metering widget for UI.</li>
</ul>

<h3>2016-05-10</h3>
<p>An update <b>1.0.7</b> that fixes some problems has been released.</p>
<ul>
    <li>Fixed the broken UI for Spektrumanalysator x4, x8, x16.</li>
    <li>Added test that displays all UI and can be launched before the build.</li>
    <li>Minimized the size of the Schlagzeug plugin series UI.</li>
</ul>

<h3>2016-05-01</h3>
<p>Version <b>1.0.6</b> has been released!</p>
<ul>
    <li>Reorganized source tree. Splitted plugin metadata into separate files and moved from core to the new directory.</li>
    <li>Fixed errors in formulas of noise envelopes for Spektrumanalysator.</li>
    <li>Fixed some bugs in DSP SSE module functions caused to possible crash plugins on some conditions.</li>
    <li>Implemented audio resampling algorithm for audio files using N-period Lanczos kernel convolution.</li>
    <li>Implemented some core primitives like Toggle, Blink etc.</li>
    <li>Added support of MIDI events for LV2.</li>
    <li>Added support of MIDI events for VST.</li>
    <li>Added support of plugin state serialization for VST.</li>
    <li>Implemented port sets for reducing plugin's port overhead.</li>
    <li>Implemented indexed proxy ports for the UI.</li>
    <li>Re-engineered LV2 Atom transport subsystem.</li>
    <li>Re-engineered LinuxVST transport subsystem.</li>
    <li>Additional feature ('quick tune') implemented for knob control (available when clicking on the knob's scale).</li>
    <li>Implemented serialization/deserialization of plugin state to plain text files (available in UI when clicking on logo or model acronym).</li>
    <li>Optimized the size of XML data (implemented 'ui:for' tag for cycles in XML documents).</li>
    <li>Optimized LV2 TTL generator for more compact RDF output, fixed some problems in RDF format.</li>
    <li>Changed the color of 'Bypass' swtich to red.</li>
    <li>Implemented Klangerzeuger (Sampler) plugin Mono/Stereo series.</li>
    <li>Implemented Schlagzeug (Multi-Sampler) plugin x12, x24, x48 Stereo/Direktausgabe (DirectOut) series.</li>
</ul>

<h3>2016-03-08</h3>
<p>Version <b>1.0.4</b> has been released!</p>
<ul>
    <li>Reduced size of mesh to 192 points for Phasendetektor.</li>
    <li>Reduced mesh refresh rate to 20 Hz.</li>
    <li>Fixed metadata for Phasendetektor (ID of 'best value' meter was mistyped).</li>
    <li>Added LV2 Units support for the plugins.</li>
    <li>Optimized some of bulk data operations with SSE.</li>
    <li>SSE-based DSP module now is built-in for all releases and dynamically turns on when possible.</li>
    <li>Implemented FFT algorithm with SSE-specific optimizations.</li>
    <li>Implemented support of ports containing file names for LV2 (LV2 Paths).</li>
    <li>Implemented support of plugin state serialization for LV2 (LV2 State).</li>
    <li>Implemented support of LV2 worker interface (LV2 Worker).</li>
    <li>Implemented support of native worker interface (based on pthreads).</li>
    <li>Implemented Spektrumanalysator (Spectrum Analyser) plugin series (x1, x2, x4, x8, x12 and x16).</li>
</ul>

<h3>2016-01-22</h3>
<p>Version <b>1.0.3</b> has been released!</p>

<p>Many refactoring and some bugfixes have been done:</p>
<ul>
    <li>Reduced mesh primitive synchronization rate to 25 Hz for LV2 Atoms.</li>
    <li>Simplified core plugin class.</li>
    <li>Simplified plugin UI class.</li>
    <li>Optimized DSP for SSE instruction set.</li>
    <li>Optimized Phasendetektor for DSP usage.</li>
    <li>Changed name of LinuxVST distribution from 'vst' to 'lxvst'.</li>
    <li>Removed dynamic_cast from C++ code and RTTI from linkage.</li>
    <li>XML documents now are built-in resources, expat library is required only for building binaries.</li>
</ul>

<h3>2016-01-19</h1>
<p>New release <b>1.0.2</b> available! Now plugins are fully compatible with <b>LinuxVST</b> plugin format!</p>
<ul>
    <li>Implemented plugin wrapping layer for more flexible plugin control.</li>
    <li>Added GUI wrapper for LinuxVST plugins.</li>
</ul>

<h3>2016-01-11</h3>
<p>Happy new year! The version <b>1.0.1</b> was released! The most significant changes:</p>
<ul>
    <li>Fixed bugs in SSE assembly code discovered at 44100 Hz sample rate.</li>
    <li>Optimized SSE DSP processor: now it doesn't need to be an instance of the class.</li>
    <li>Fixed assertion issues with GTK+ support on UI close and destroy for LV2.</li>
    <li>Implemented generic LinuxVST support for plugins. Currently UI is not supported.</li>
    <li>Updated plugin metadata to become more compatible with VST.</li>
</ul>

<h3>2015-12-27</h3>
<p>Project officially announced, the first demo video is available on the <a href="/?page=video">video page</a>.</p>

<h3>2015-12-21</h3>
<p>The version 1.0.0 of the binary plugin package has been released.</p>
<p>You may get the release files from the <a href="/?page=download">download page</a>.</p>

<h3>2015-12-14</h3>
<p>The project becomes it's own domain name and site.<p>

<h3>2015-10-09</h3>
<p>The project has been started. The first commit with initial import was made to the code repository.<p>