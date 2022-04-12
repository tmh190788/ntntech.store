<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div class="website-wrapper">
 *
 * @package xts
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
		<?php wp_head(); ?>
	</head>

	<?php do_action( 'xts_before_body' ); ?>

	<body <?php body_class(); ?>>

		<?php wp_body_open(); ?>

		<?php do_action( 'xts_before_site_wrapper' ); ?>

		<div class="xts-site-wrapper">

			<?php do_action( 'xts_before_header' ); ?>

			<?php if ( ! function_exists( 'elementor_theme_do_location' ) || ! elementor_theme_do_location( 'header' ) ) : ?>
				<header class="xts-header <?php echo esc_attr( xts_get_header_classes() ); ?>">
					<?php do_action( 'xts_header' ); ?>
				</header>
			<?php endif ?>

			<?php do_action( 'xts_after_header' ); ?>

			<?php xts_page_top_part(); ?>
