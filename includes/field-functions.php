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
	<div class="novelist-box-row">
		<label><?php _e( 'Review Excerpts', 'novelist-review-excerpts' ); ?></label>
		<div class="novelist-input-wrapper">
			<div id="novelist-review-excerpts" class="novelist-repeatable-group novelist-repeater-sortable">
				<?php foreach ( $quotes as $key => $quote ) :
					$defaults = array(
						'reviewer' => '',
						'url'      => '',
						'quote'    => ''
					);

					$quote = wp_parse_args( $quote, $defaults );
					?>

					<div class="novelist-repeater-section" data-iterator="<?php echo esc_attr( absint( $key ) ); ?>">
						<div class="novelist-repeater-heading">
							<span class="dashicons novelist-repeater-toggle"></span>
							<span class="dashicons dashicons-trash novelist-repeater-remove" data-selector="#novelist-review-excerpts"></span>
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
								<input type="url" id="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_url" name="novelist_review_quotes[<?php echo esc_attr( $key ); ?>][url]" placeholder="http://" value="<?php echo esc_url( $quote['url'] ); ?>">
							</div>
						</div>
						<div class="novelist-box-row">
							<label for="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_quote"><?php _e( 'Excerpt', 'novelist-review-excerpts' ); ?></label>
							<div class="novelist-input-wrapper">
								<textarea id="novelist_review_quotes_<?php echo esc_attr( $key ); ?>_quote" name="novelist_review_quotes[<?php echo esc_attr( $key ); ?>][quote]" rows="10"><?php echo esc_textarea( $quote['quote'] ); ?></textarea>
							</div>
						</div>
						<div class="novelist-box-row novelist-repeater-actions">
							<button class="button novelist-repeater-remove" data-selector="#novelist-review-excerpts"><?php _e( 'Remove Review', 'novelist-review-excerpts' ); ?></button>
						</div>
					</div>
				<?php endforeach; ?>

				<div class="novelist-box-row">
					<button data-selector="#novelist-review-excerpts" data-group-title="<?php esc_attr_e( 'Review #{#}', 'novelist-review-excerpts' ); ?>" class="novelist-add-repeater-section button"><?php _e( 'Add Review', 'novelist-review-excerpts' ); ?></button>
				</div>
			</div>
		</div>
	</div>
	<?php
}

add_action( 'novelist/meta-box/display-field-review_excerpts', 'novelist_review_excerpts_meta_box', 10, 3 );

/**
 * Add `novelist_review_quotes` as a field to be saved.
 *
 * @param array $fields Array of name attributes to be located in $_POST and saved.
 *
 * @since 1.0.0
 * @return array
 */
function novelist_review_excerpts_save_fields( $fields ) {
	$fields[] = 'novelist_review_quotes';

	return $fields;
}

add_filter( 'novelist/book/meta-box/saved-fields', 'novelist_review_excerpts_save_fields' );

/**
 * Sanitize Review Excerpts
 *
 * @param array $input
 *
 * @since 1.0.0
 * @return array
 */
function novelist_review_excerpts_sanitize_field( $input ) {
	$sanitized_fields = array();

	if ( ! is_array( $input ) ) {
		$input = array();
	}

	foreach ( $input as $key => $quote ) {
		$sanitized_fields[ $key ]['reviewer'] = array_key_exists( 'reviewer', $quote ) ? sanitize_text_field( $quote['reviewer'] ) : '';
		$sanitized_fields[ $key ]['url']      = array_key_exists( 'url', $quote ) ? esc_url_raw( $quote['url'] ) : '';
		$sanitized_fields[ $key ]['quote']    = array_key_exists( 'quote', $quote ) ? wp_kses_post( $quote['quote'] ) : '';
	}

	return $sanitized_fields;
}

add_filter( 'novelist/book/meta-box/sanitize/novelist_review_quotes', 'novelist_review_excerpts_sanitize_field' );

/**
 * Render Review Excerpts
 *
 * @param string        $value          Current value for this field
 * @param string        $key            The key that is being filtered
 * @param array         $all_fields     All available book fields
 * @param array         $enabled_fields Array of the enabled book fields
 * @param Novelist_Book $book           Object for the current book
 *
 * @since 1.0.0
 * return string
 */
function novelist_review_excerpts_render_field( $value, $key, $all_fields, $enabled_fields, $book ) {

	$quotes = $book->get_value( 'novelist_review_quotes' );

	if ( ! $quotes || ! is_array( $quotes ) || ! count( $quotes ) || ( count( $quotes ) == 1 && ! $quotes[0]['quote'] ) ) {
		return false;
	}

	$final_value = '';

	foreach ( $quotes as $quote ) {

	}

	return $final_value;

}

add_filter( 'novelist/book/pre-render/review_excerpts', 'novelist_review_excerpts_render_field', 10, 5 );