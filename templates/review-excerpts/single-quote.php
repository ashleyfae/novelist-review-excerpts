<?php
/**
 * Single Quote Template
 *
 * The variable `$quote` is available here. It's an array with the following keys:
 *
 *  `reviewer` - string - Name of the reviewer.
 *  `url` - string - URL to the review.
 *  `quote` - string - The actual quote.
 *
 * If you want to override this template, create a new folder in your theme called `novelist_templates`.
 * Then, inside that folder, create another called `review-excerpts`. Then paste this file inside that
 * folder. You can then modify it to your liking.
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

// Set up the default values to make sure we have all the array keys.
$defaults = array(
	'reviewer' => '',
	'url'      => '',
	'quote'    => ''
);

$quote = wp_parse_args( $quote, $defaults );

// Bail if we don't actually have a quote filled out.
if ( ! $quote['quote'] ) {
	return;
}

/*
 * Actual display starts here.
 */
?>
<blockquote class="novelist-review-quote">
	<?php echo wpautop( $quote['quote'] ); ?>

	<?php if ( $quote['reviewer'] ) : ?>
		<cite>
			<?php
			// Open the link tag.
			if ( $quote['url'] ) {
				echo '<a href="' . esc_url( $quote['url'] ) . '" target="_blank">';
			}

			// Name of the reviewer.
			echo esc_html( $quote['reviewer'] );

			// Close the link tag.
			if ( $quote['url'] ) {
				echo '</a>';
			}
			?>
		</cite>
	<?php endif; ?>
</blockquote>