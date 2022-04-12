<?php
/**
 * Footer config function
 *
 * @version 1.0
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

return apply_filters(
	'xts_footer_configs_array',
	array(
		1  => array(
			'cols' => array(
				'col-12',
			),
		),
		2  => array(
			'cols' => array(
				'col-12 col-sm-6',
				'col-12 col-sm-6',
			),
		),
		3  => array(
			'cols' => array(
				'col-12 col-sm-4',
				'col-12 col-sm-4',
				'col-12 col-sm-4',
			),
		),
		4  => array(
			'cols' => array(
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-3',
			),
		),
		5  => array(
			'cols' => array(
				'col-12 col-sm-6 col-lg-2-5',
				'col-12 col-sm-6 col-lg-2-5',
				'col-12 col-sm-4 col-lg-2-5',
				'col-12 col-sm-4 col-lg-2-5',
				'col-12 col-sm-4 col-lg-2-5',
			),
		),
		6  => array(
			'cols' => array(
				'col-12 col-sm-6 col-md-4 col-lg-2',
				'col-12 col-sm-6 col-md-4 col-lg-2',
				'col-12 col-sm-6 col-md-4 col-lg-2',
				'col-12 col-sm-6 col-md-4 col-lg-2',
				'col-12 col-sm-6 col-md-4 col-lg-2',
				'col-12 col-sm-6 col-md-4 col-lg-2',
			),
		),
		13 => array(
			'cols' => array(
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
			),
		),
		14 => array(
			'cols' => array(
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-3',
			),
		),
		16 => array(
			'cols' => array(
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-4 col-lg-2',
				'col-12 col-sm-6 col-lg-3',
				'col-12 col-sm-6 col-lg-3',
			),
		),
	)
);
