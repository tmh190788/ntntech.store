<?php
/**
 * links for theme settings and elements.
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_links_theme_settings_array',
	array(
		'welcome_all_themes'          => 'https://themeforest.net/user/xtemos/portfolio',
		'welcome_forum'               => 'https://themeforest.net/user/xtemos/portfolio',
		'activation_find_license_key' => 'https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-',
		'activation_purchase'         => 'https://themeforest.net/user/xtemos/portfolio',
		'activation_go_to_account'    => '#',
	)
);
