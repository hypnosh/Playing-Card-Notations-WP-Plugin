<?php
/**
 * Plugin Name: Playing Card Notations
 * Plugin URI: https://www.recaptured.in/playing-card-notations
 * Description: Easily display playing card notations in your blog
 * Version: 1.0
 * Author: Amit Sharma
 * Author URI: https://www.recaptured.in/
 */

add_shortcode('pcards', 'add_playing_card_notations');

function add_playing_card_notations($atts) {
	$a = shortcode_atts( array(
			'content'		=> 'AhKc',
			'layout'		=> 'inline',
		), $atts);


	/* atts
		c = content, e.g. Ac7d
		layout = in, left, right, centre
	*/
	/*
		A K Q J T/10 9 8 7 6 5 4 3 2
		s c h d
		&spades;
		&clubs;
		&hearts;
		&diams;
		&#x1f0a1;
	*/
	if ($a->layout == "inline") {
		// unicode cards
	} else {
		// rich cards, non-inline	
		$suites = [
			"s" => "<span class='pc-suite pc-suite-spades pc-suite-blacks'>&spades;</span>",
			"c" => "<span class='pc-suite pc-suite-clubs pc-suite-blacks'>&clubs;</span>",
			"h" => "<span class='pc-suite pc-suite-hearts pc-suite-reds'>&hearts;</span>",
			"d" => "<span class='pc-suite pc-suite-diams pc-suite-reds'>&diams;</span>",
		];
		$out = $a->content; 
		foreach ($suites as $key => $value) {
			// replace the suite keys with suite code
			$out = str_replace(&key, &value, $out);
		}
	}
	$out = "<span class='pcards-block'>" . $out . "</span>";
	return $out;
} // add_playing_card_notations

?>
<div class="wrap">
	<h1>Playing Card Notations</h1>
	<form action="options.php" method="post">
		<?php
			settings_fields('pcard_settings');
			do_settings_sections('pcard_settings');
		?>
		<!-- options for card styles -->
		<?php submit_button(); ?>
		<?
	</form>
</div>
