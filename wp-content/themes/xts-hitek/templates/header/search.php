<?php
/**
 * Search element template
 *
 * @package xts
 */

$wrapper_classes      = '';
$icon_classes         = '';
$form_wrapper_classes = '';
$dropdown_classes     = '';
$count                = 'dropdown' === $params['display'] ? 20 : 40;
$icon_type            = $params['icon_type'];
$color_scheme         = $params['color_scheme'];

$wrapper_classes .= ' xts-display-' . $params['display'];
if ( 'dropdown' === $params['display'] ) {
	xts_enqueue_js_script( 'search-dropdown' );
}
if ( 'full-screen' === $params['display'] ) {
	xts_enqueue_js_script( 'search-full-screen' );
}
$wrapper_classes .= ' xts-style-' . $params['icon_style'];
if ( 'dropdown' === $params['display'] ) {
	$wrapper_classes .= ' xts-event-click';
}

$form_wrapper_classes .= ' xts-header-search-form';
if ( 'inherit' !== $params['form_color_scheme'] ) {
	$form_wrapper_classes .= ' xts-scheme-' . $params['form_color_scheme'] . '-form';
}
if ( isset( $params['form_width'] ) && $params['form_width'] ) {
	$form_wrapper_classes .= ' xts-width-' . $params['form_width'];
}

if ( 'custom' === $icon_type ) {
	$icon_classes .= ' xts-icon-custom';
}

if ( 'light' === $color_scheme && $color_scheme ) {
	$dropdown_classes .= ' xts-scheme-' . $color_scheme;
}

if ( 'form' === $params['display'] ) {
	$search_style = isset( $params['search_style'] ) ? $params['search_style'] : 'default';
	xts_search_form(
		array(
			'ajax'                => $params['ajax'],
			'count'               => $params['ajax_result_count'],
			'post_type'           => $params['post_type'],
			'icon_type'           => $icon_type,
			'search_style'        => $search_style,
			'custom_icon'         => $params['custom_icon'],
			'wrapper_classes'     => $form_wrapper_classes,
			'dropdown_classes'    => $dropdown_classes,
			'categories_dropdown' => isset( $params['categories_dropdown'] ) && $params['categories_dropdown'] ? 'yes' : 'no',
		)
	);

	return;
}

?>

<div class="xts-header-search xts-header-el<?php echo esc_attr( $wrapper_classes ); ?>">
	<a href="#">
		<span class="xts-header-el-icon<?php echo esc_attr( $icon_classes ); ?>">
			<?php if ( 'custom' === $icon_type ) : ?>
				<?php echo xts_get_custom_icon( $params['custom_icon'] ); // phpcs:ignore ?>
			<?php endif; ?>
		</span>

		<span class="xts-header-el-label">
			<?php esc_html_e( 'Search', 'xts-theme' ); ?>
		</span>
	</a>

	<?php do_action( 'xts_header_builder_search_after_icon' ); ?>

	<?php if ( 'dropdown' === $params['display'] ) : ?>
		<?php
		$style_dropdown_wrapper_classes = '';

		if ( 'light' === $color_scheme && $color_scheme ) {
			$style_dropdown_wrapper_classes .= 'xts-scheme-light-form xts-scheme-light';
		} else {
			$style_dropdown_wrapper_classes .= 'xts-scheme-dark-form';
		}

		xts_search_form(
			array(
				'ajax'             => $params['ajax'],
				'count'            => $params['ajax_result_count'],
				'post_type'        => $params['post_type'],
				'type'             => 'dropdown',
				'icon_type'        => $icon_type,
				'custom_icon'      => $params['custom_icon'],
				'search_style'     => 'icon-alt',
				'wrapper_classes'  => $style_dropdown_wrapper_classes,
				'dropdown_classes' => $dropdown_classes,
			)
		);
		?>
	<?php endif ?>
</div>
