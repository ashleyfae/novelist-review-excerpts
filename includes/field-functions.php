<?php
/**
 * Functions for adding new fields to the Novelist plugin.
 *
 * @package   novelist-review-excerpts
 * @copyright Copyright (c) 2016, Nose Graze Ltd.
 * @license   GPL2+
 */

/**
 * Add the "Review Excerpts" field to the available book layout options.
 *
 * @param array $fields
 *
 * @since 1.0.0
 * @return array
 */
function novelist_review_excerpts_field( $fields ) {
	$fields['review_excerpts'] = array(
		'name'        => __( 'Review Excerpts', 'novelist-review-excerpts' ),
		'placeholder' => '[review_excerpts]',
		'label'       => '[review_excerpts]',
		'linebreak'   => false
	);

	return $fields;
}

add_filter( 'novelist/book/available-fields', 'novelist_review_excerpts_field' );

/**
 * Render the admin area text box
 *
 * @param Novelist_Book $book     Book object.
 * @param WP_Post       $post     Current post object.
 * @param array         $settings Array of settings for this field. Includes the 'label' and 'linebreak'.
 *
 * @return void
 */
function novelist_review_excerpts_meta_box( $book, $post, $settings ) {
	$quotes = $book->get_value( 'novelist_review_quotes' );
	$quotes = ( is_array( $quotes ) && count( $quotes ) ) ? $quotes : array(
		array(
			'reviewer' => '',
			'url'      => '',
			'quote'    => ''
		)
	);
	?>
	<div id="novelist-review-excerpts">
		<?php foreach ( $quotes as $key => $quote ) : ?>
			<div class="novelist-review-excerpts-section" data-iterator="<?php echo esc_attr( absint( $key ) ); ?>">
				<div class="novelist-review-excerpts-heading">
					<span class="dashicons novelist-review-excerpts-toggle"></span>
					<h3><?php printf( __( 'Review #%s', 'novelist-review-excerpts' ), absint( $key ) + 1 ); ?></h3>
				</div>

				<div class="novelist-box-row">
					<label for="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_reviewer"><?php _e( 'Reviewer', 'novelist-review-excerpts' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_reviewer" name="novelist_review_quotes[<?php echo esc_attr( $key ); ?>][reviewer]" value="<?php echo esc_attr( $quote['reviewer'] ); ?>">
					</div>
				</div>
				<div class="novelist-box-row">
					<label for="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_url"><?php _e( 'URL', 'novelist-review-excerpts' ); ?></label>
					<div class="novelist-input-wrapper">
						<input type="text" id="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_url" name="novelist_review_quotes[<?php echo esc_attr( $key ); ?>][url]" value="<?php echo esc_url( $quote['url'] ); ?>">
					</div>
				</div>
				<div class="novelist-box-row">
					<label for="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_review"><?php _e( 'Excerpt', 'novelist-review-excerpts' ); ?></label>
					<div class="novelist-input-wrapper">
						<textarea id="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_review" name="novelist_review_quotes[<?php echo esc_attr( $key ); ?>][review]" rows="10"><?php echo esc_textarea( $quote['quote'] ); ?></textarea>
					</div>
				</div>
				<div class="novelist-box-row novelist-review-excerpts-actions">
					<button class="button novelist-review-excerpts-remove"><?php _e( 'Remove Quote', 'novelist-review-excerpts' ); ?></button>
				</div>
			</div>
		<?php endforeach; ?>

		<div class="novelist-box-row">
			<button data-selector="#novelist-review-excerpts" data-group-title="<?php esc_attr_e( 'Quote {#}', 'novelist-review-excerpts' ); ?>" class="novelist-add-review button"><?php _e( 'Add Review', 'novelist-review-excerpts' ); ?></button>
		</div>
	</div>
	<?php
}

add_action( 'novelist/meta-box/display-field-review_excerpts', 'novelist_review_excerpts_meta_box', 10, 3 );