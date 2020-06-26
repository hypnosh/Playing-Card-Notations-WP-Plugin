<?php
/**
 * Plugin Name: Playing Card Notations (PCN)
 * Plugin URI: https://www.recaptured.in/new-wordpress-plugin-playing-card-notations
 * Description: Easily display playing card notations in your blog
 * Version: 1.2
 * Author: Amit Sharma
 * Author URI: https://www.recaptured.in/
 */

add_shortcode('pcards', 'pcards_add_playing_card_notations');
add_shortcode('pcn', 'pcards_add_playing_card_notations');

function pcards_add_playing_card_notations($atts, $content = null) {
	$pcards_colours = esc_attr(get_option('pcards-suite-colours'));
	$pcards_style = esc_attr(get_option('pcards-suite-style'));

	$a = shortcode_atts( array(
			'layout'		=> 'inline',
			'style'			=> ($pcards_colours . "-" . $pcards_style),
		), $atts);


	/*
		A K Q J T/10 9 8 7 6 5 4 3 2
		s c h d		&clubs;
		&hearts;
		&diams;
		&#x1f0a1;
	*/

	$layout = $a['layout'];
	$style = $a['style'];

	// rich cards, non-inline	
	$style_codes = [
		"st1"		=> "brw",
		"st2"		=> "brr",
		"st3"		=> "brgpw",
		"st4"		=> "brgpr",
		"default"	=> "brw",
		"reverse"	=> "brr",
		"alternate"	=> "brgpw",
		"altrev"	=> "brgpr",
	];
	$suites_symbols = [
		"s" => "-&/",
		"c" => "-*/",
		"h" => "-$/",
		"d" => "-#/"
	];
	$suites = [
		"&" => "spades",
		"*" => "clubs",
		"$" => "hearts",
		"#" => "diams",
	];
	$suite_names = [
		"&" => "spades",
		"*" => "clubs",
		"$" => "hearts",
		"#" => "diamonds",
	];
	$rank_names = [
		"2"		=> "two", 
		"3"		=> "three", 
		"4"		=> "four", 
		"5"		=> "five", 
		"6"		=> "six", 
		"7"		=> "seven", 
		"8"		=> "eight", 
		"9"		=> "nine", 
		"10"	=> "ten", 
		"T"		=> "ten", 
		"J"		=> "jack", 
		"Q"		=> "queen", 
		"K"		=> "king", 
		"A"		=> "ace",
	];
	
	foreach ($suites_symbols as $key => $value) {
		// replace the suite keys with suite mnemonic
		$content = str_replace($key, $value, $content);
	}
	// segregate as cards
	$cards = explode("/", $content);
	array_pop($cards);

	if (!is_array($cards) or count($cards) == 0) { // no valid suite found
		return "[invalid notations]";
	}
	foreach ($cards as $key => $value) {
		$card_exploded = explode("-", $value);
		$rank = $card_exploded[0];
		if ($rank == "T") $rank = "10";

		$suite = $suites[$card_exploded[1]];
		$rank_name = ucwords($rank_names[$rank]);
		if (!array_key_exists($rank, $rank_names)) { // invalid rank/suite characters found
			return "[invalid notations]";
		}
		$suite_name = ucwords($suite_names[$card_exploded[1]]);

		$o[] = "<span class='pccard pccard-" . $suite . " pccard-" . $layout .
					"' title='" . $rank_name . " of " . $suite_name . "'>" . 
					"<span class='pcrank'>" . $rank . "</span>" .
					"<span class='pcsuite pcsuite-" . $suite . "'>&" . $suite . ";</span>" . 
					// "<span class='pcrank-large'>" . $rank . "</span>" .
				"</span>";
	}
	$out = implode("", $o);
	// foreach ($suites as $key => $value) {
	// 	// replace the suite keys with suite code
	// 	$out = str_replace($key, $value, $out);
	// }
	// $style = $style_codes[$style];
	$pcards_font = esc_attr(get_option('pcards-font'));
	$out = "<span class='pcblock pcblock-" . $layout . " pcblock-" . $style .  " pcblock-font-" . $pcards_font . "'>" . $out . "</span>";
	return $out;
} // pcards_add_playing_card_notations


add_action('wp_enqueue_scripts', 'pcards_enqueue_styles');

function pcards_enqueue_styles() {
	wp_enqueue_style( 'pcards_style', plugins_url('pcards.css', __FILE__) );
	$pcards_font = esc_attr(get_option('pcards-font'));

	$pcards_font_array = [
		'Roboto_Condensed' => 'Roboto+Condensed:wght@700',
		'Fira_Sans_Condensed' => 'Fira+Sans+Condensed:wght@500',
		'Open_Sans_Condensed' => 'Open+Sans+Condensed:wght@700',
		'Barlow_Condensed' => 'Barlow+Condensed:wght@600',
		'Ubuntu_Condensed' => 'Ubuntu+Condensed',
		'Asap_Condensed' => 'Asap+Condensed:wght@600',
		'IBM_Plex_Sans_Condensed' => 'IBM+Plex+Sans+Condensed:wght@600'
	];

	$pcards_font_slug = 'pcards-' . $pcards_font;
	$pcard_font_url = "//fonts.googleapis.com/css2?family=" . $pcards_font_array[$pcards_font] . "&display=swap";
	wp_enqueue_style( $pcards_font_slug, $pcard_font_url );
} // pcards_enqueue_scripts

add_action( 'admin_enqueue_scripts', 'pcards_admin_enqueue_styles' );
function pcards_admin_enqueue_styles() {
	wp_enqueue_script( 'pcards_script_admin', plugins_url('pcards-admin.js', __FILE__) );
	wp_enqueue_style( 'pcards_style', plugins_url('pcards.css', __FILE__) );
	wp_enqueue_style( 'pcards-roboto-condensed', "//fonts.googleapis.com/css2?family=Roboto+Condensed:wght@700&display=swap" );
	wp_enqueue_style( 'pcards-fira-sans-condensed', "//fonts.googleapis.com/css2?family=Fira+Sans+Condensed:wght@500&display=swap" );
	wp_enqueue_style( 'pcards-open-sans-condensed', "//fonts.googleapis.com/css2?family=Open+Sans+Condensed:wght@700&display=swap" );
	wp_enqueue_style( 'pcards-barlow-condensed', "//fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600&display=swap" );
	wp_enqueue_style( 'pcards-ubuntu-condensed', "//fonts.googleapis.com/css2?family=Ubuntu+Condensed&display=swap" );
	wp_enqueue_style( 'pcards-asap-condensed', "//fonts.googleapis.com/css2?family=Asap+Condensed:wght@600&display=swap" );
	wp_enqueue_style( 'pcards-ibm-plex-condensed', "//fonts.googleapis.com/css2?family=IBM+Plex+Sans+Condensed:wght@600&display=swap" );
} // pcards_admin_enqueue_styles


add_action('admin_init', 'pcards_register_settings');
add_action('admin_menu', 'pcards_add_settings_menu');

function pcards_add_settings_menu() {
	add_submenu_page('themes.php', 'Playing Card Notations Settings', 'Playing Card Notations Settings', 'administrator', 'pcards-settings-menu', 'pcards_settings_page');
} // pcards_add_settings_menu

function pcards_register_settings() {
	register_setting( 'pcards-group', 'pcards-font' );
	register_setting( 'pcards-group', 'pcards-suite-colours' );
	register_setting( 'pcards-group', 'pcards-suite-style' );
} // pcards_register_settings

function pcards_settings_page() {
	?>
	<div class="wrap">
		<h1>Playing Card Notations - Settings</h1>
		<form method="post" action="options.php">
			<?php
				settings_fields( 'pcards-group' );
				do_settings_sections( 'pcards-group' );
			?>
			<table class="form-table">
				<tr valign="top">
					<th scopr="row" class="pcards-th">How to Use PCN</th>
				</tr>
				<tr valign="top">
					<td>
						<p>Thanks for installing PCN!</p>
						<p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="pcards-th">Font</th>
				</tr>
				<tr valign="top">
					<td>
						<!-- font options -->
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Roboto_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Roboto_Condensed") echo "checked"; ?>><label for="Roboto_Condensed" class="pcblock-font-option pcblock-font-Roboto_Condensed">Roboto Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Fira_Sans_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Fira_Sans_Condensed") echo "checked"; ?>><label for="Fira_Sans_Condensed" class="pcblock-font-option pcblock-font-Fira_Sans_Condensed">Fira Sans Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Open_Sans_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Open_Sans_Condensed") echo "checked"; ?>><label for="Open_Sans_Condensed" class="pcblock-font-option pcblock-font-Open_Sans_Condensed">Open Sans Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Barlow_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Barlow_Condensed") echo "checked"; ?>><label for="Barlow_Condensed" class="pcblock-font-option pcblock-font-Barlow_Condensed">Barlow Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Ubuntu_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Ubuntu_Condensed") echo "checked"; ?>><label for="Ubuntu_Condensed" class="pcblock-font-option pcblock-font-Ubuntu_Condensed">Ubuntu Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="Asap_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "Asap_Condensed") echo "checked"; ?>><label for="Asap_Condensed" class="pcblock-font-option pcblock-font-Asap_Condensed">Asap Condensed</label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-font" value="IBM_Plex_Sans_Condensed" <?php if(esc_attr(get_option('pcards-font')) == "IBM_Plex_Sans_Condensed") echo "checked"; ?>><label for="IBM_Plex_Sans_Condensed" class="pcblock-font-option pcblock-font-IBM_Plex_Sans_Condensed">IBM Plex Sans Condensed</label>
						</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="pcards-th">Colours</th>
				</tr>
				<tr valign="top">
					<td>
						<!-- colour options -->
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="standard" <?php if(esc_attr(get_option('pcards-suite-colours')) == "standard") echo "checked"; ?>><label for="standard"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-standard-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">Standard Two Colour</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="4color" <?php if(esc_attr(get_option('pcards-suite-colours')) == "4color") echo "checked"; ?>><label for="4color"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-4color-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">Standard Four Colour</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="English-poker" <?php if(esc_attr(get_option('pcards-suite-colours')) == "English-poker") echo "checked"; ?>><label for="English-poker"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-English-poker-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">English Poker</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="German-skat" <?php if(esc_attr(get_option('pcards-suite-colours')) == "German-skat") echo "checked"; ?>><label for="German-skat"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-German-skat-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">German Skat</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="English-bridge" <?php if(esc_attr(get_option('pcards-suite-colours')) == "English-bridge") echo "checked"; ?>><label for="English-bridge"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-English-bridge-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">English Bridge</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="american" <?php if(esc_attr(get_option('pcards-suite-colours')) == "american") echo "checked"; ?>><label for="american"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-american-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">American Centennial, Mauger</span></label>
						</span>
						<span class="pcards-options">
						<input type="radio" name="pcards-suite-colours" value="Seminolewars-bridge" <?php if(esc_attr(get_option('pcards-suite-colours')) == "Seminolewars-bridge") echo "checked"; ?>><label for="Seminolewars-bridge"><span class="pcblock pcblock-inline pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?> pcblock-Seminolewars-bridge-standard"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span><span class="pccard pccard-diams pccard-inline" title="King of Diamonds"><span class="pcrank">K</span><span class="pcsuite pcsuite-diams">&diams;</span></span><span class="pccard pccard-clubs pccard-inline" title="Eight of Clubs"><span class="pcrank">8</span><span class="pcsuite pcsuite-clubs">&clubs;</span></span><span class="pccard pccard-hearts pccard-inline" title="Two of Hearts"><span class="pcrank">2</span><span class="pcsuite pcsuite-hearts">&hearts;</span></span></span><br/><span class="pcards-colour-label">Seminole Wars Bridge</span></label>
						</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="pcards-th">Style</th>
				</tr>
				<tr valign="top">
					<td>
						<!-- style options -->
						<span class="pcards-options"><input type="radio" name="pcards-suite-style" value="standard" <?php if(esc_attr(get_option('pcards-suite-style')) == "standard") echo "checked"; ?>><label for="standard"><span class="pcblock pcblock-inline pcblock-<?php echo esc_attr(get_option('pcards-suite-colours')); ?>-standard pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?>"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span></span></label></span>
						<span class="pcards-options"><input type="radio" name="pcards-suite-style" value="reverse" <?php if(esc_attr(get_option('pcards-suite-style')) == "reverse") echo "checked"; ?>><label for="reverse"><span class="pcblock pcblock-inline pcblock-<?php echo esc_attr(get_option('pcards-suite-colours')); ?>-reverse pcblock-font-<?php echo esc_attr(get_option('pcards-font')); ?>"><span class="pccard pccard-spades pccard-inline" title="Ace of Spades"><span class="pcrank">A</span><span class="pcsuite pcsuite-spades">&spades;</span></span></span></label></span>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		</form>
		</div><!-- .wrap -->

	<?php
} // pcards_settings_page
?>