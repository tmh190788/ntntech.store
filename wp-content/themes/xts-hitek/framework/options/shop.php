<?php
/**
 * Shop options
 *
 * @package xts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

use XTS\Framework\Options;

/**
 * Shop.
 */
Options::add_field(
	array(
		'id'          => 'action_after_add_to_cart',
		'name'        => esc_html__( 'Action after add to cart', 'xts-theme' ),
		'description' => esc_html__( 'Choose between showing informative popup and opening shopping cart widget.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'general_shop_section',
		'options'     => array(
			'no-action' => array(
				'name'  => esc_html__( 'No action', 'xts-theme' ),
				'value' => 'small',
			),
			'popup'     => array(
				'name'  => esc_html__( 'Popup', 'xts-theme' ),
				'value' => 'popup',
			),
			'widget'    => array(
				'name'  => esc_html__( 'Widget', 'xts-theme' ),
				'value' => 'widget',
			),
		),
		'default'     => 'widget',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'action_after_add_to_cart_timeout',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Hide widget automatically', 'xts-theme' ),
		'description' => esc_html__( 'After adding to cart the shopping cart widget will be hidden automatically.', 'xts-theme' ),
		'section'     => 'general_shop_section',
		'requires'    => array(
			array(
				'key'     => 'action_after_add_to_cart',
				'compare' => 'not_equals',
				'value'   => 'no-action',
			),
		),
		'default'     => '0',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'action_after_add_to_cart_timeout_number',
		'name'        => esc_html__( 'Hide widget after', 'xts-theme' ),
		'description' => esc_html__( 'Set the number of seconds for the shopping cart widget to be displayed after adding to cart', 'xts-theme' ),
		'type'        => 'range',
		'section'     => 'general_shop_section',
		'default'     => 3,
		'min'         => 3,
		'max'         => 20,
		'step'        => 1,
		'requires'    => array(
			array(
				'key'     => 'action_after_add_to_cart_timeout',
				'compare' => 'equals',
				'value'   => '1',
			),
			array(
				'key'     => 'action_after_add_to_cart',
				'compare' => 'not_equals',
				'value'   => 'no-action',
			),
		),
		'priority'    => 30,
	)
);

/**
 * Catalog mode catalog_mode (40).
 */

/**
 * Login to see add to cart and price login_to_see_price (50).
 */

Options::add_field(
	array(
		'id'          => 'checkout_steps',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Checkout steps', 'xts-theme' ),
		'description' => esc_html__( 'Display three steps on shopping cart and checkout. Shopping cart -> Checkout -> Order complete.', 'xts-theme' ),
		'section'     => 'general_shop_section',
		'default'     => '1',
		'priority'    => 60,
	)
);

/**
 * Wc my account links my_account_links (70)
 */

/**
 * Size guide single_product_size_guide (80)
 */

/**
 * Quantity input WC_Mini_Cart_Quantity (90)
 */

Options::add_field(
	array(
		'id'       => 'product_hover_image',
		'type'     => 'switcher',
		'name'     => esc_html__( 'Hover image', 'xts-theme' ),
		'section'  => 'general_shop_section',
		'default'  => '1',
		'priority' => 110,
	)
);

Options::add_field(
	array(
		'id'          => 'empty_cart_text',
		'type'        => 'textarea',
		'wysiwyg'     => false,
		'name'        => esc_html__( 'Empty cart text', 'xts-theme' ),
		'description' => esc_html__( 'Text will be displayed if user don\'t add any products to cart.', 'xts-theme' ),
		'default'     => 'Before proceed to checkout you must add some products to your shopping cart.<br> You will find a lot of interesting products on our "Shop" page.',
		'section'     => 'general_shop_section',
		'priority'    => 120,
	)
);

/**
 * Product labels.
 */
Options::add_field(
	array(
		'id'       => 'product_label_shape',
		'type'     => 'buttons',
		'name'     => esc_html__( 'Label shape', 'xts-theme' ),
		'section'  => 'product_labels_section',
		'options'  => array(
			'round'     => array(
				'name'  => esc_html__( 'Round', 'xts-theme' ),
				'value' => 'round',
			),
			'rounded'   => array(
				'name'  => esc_html__( 'Rounded', 'xts-theme' ),
				'value' => 'rounded',
			),
			'rectangle' => array(
				'name'  => esc_html__( 'Rectangle', 'xts-theme' ),
				'value' => 'rectangle',
			),
		),
		'default'  => 'round',
		'priority' => 10,
	)
);

Options::add_field(
	array(
		'id'          => 'product_label_percentage',
		'type'        => 'switcher',
		'name'        => esc_html__( 'Shop sale label in percentage', 'xts-theme' ),
		'description' => esc_html__( 'Works with Simple, Variable and External products only.', 'xts-theme' ),
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 20,
	)
);

Options::add_field(
	array(
		'id'          => 'product_label_new',
		'type'        => 'switcher',
		'name'        => esc_html__( '"New" label on products', 'xts-theme' ),
		'description' => esc_html__( 'This label is displayed for products if you enabled this option for particular items.', 'xts-theme' ),
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'product_label_hot',
		'type'        => 'switcher',
		'name'        => esc_html__( '"Hot" label on products', 'xts-theme' ),
		'description' => esc_html__( 'Your products marked as "Featured" will have a badge with "Hot" label.', 'xts-theme' ),
		'section'     => 'product_labels_section',
		'default'     => '1',
		'priority'    => 40,
	)
);

/**
 * Thank you page.
 */
Options::add_field(
	array(
		'id'          => 'thank_you_page_content_type',
		'name'        => esc_html__( 'Content type', 'xts-theme' ),
		'description' => esc_html__( 'You can display content as a simple text or if you need more complex structure you can create an HTML Block with Elementor builder and place it here.', 'xts-theme' ),
		'type'        => 'buttons',
		'section'     => 'thank_you_page_section',
		'options'     => array(
			'text'       => array(
				'name'  => esc_html__( 'Text', 'xts-theme' ),
				'value' => 'text',
			),
			'html_block' => array(
				'name'  => esc_html__( 'HTML Block', 'xts-theme' ),
				'value' => 'html_block',
			),
		),
		'default'     => 'text',
		'priority'    => 10,
	)
);

Options::add_field(
	array(
		'id'       => 'thank_you_page_text',
		'name'     => esc_html__( 'Text', 'xts-theme' ),
		'type'     => 'textarea',
		'wysiwyg'  => true,
		'section'  => 'thank_you_page_section',
		'requires' => array(
			array(
				'key'     => 'thank_you_page_content_type',
				'compare' => 'equals',
				'value'   => 'text',
			),
		),
		'priority' => 20,
	)
);

Options::add_field(
	array(
		'id'           => 'thank_you_page_html_block',
		'name'         => esc_html__( 'HTML Block', 'xts-theme' ),
		'description'  => '<a href="' . esc_url( admin_url( 'post.php?post=' ) ) . '" class="xts-edit-block-link" target="_blank">' . esc_html__( 'Edit this block with Elementor', 'xts-theme' ) . '</a>',
		'type'         => 'select',
		'section'      => 'thank_you_page_section',
		'empty_option' => true,
		'select2'      => true,
		'options'      => xts_get_html_blocks_array(),
		'requires'     => array(
			array(
				'key'     => 'thank_you_page_content_type',
				'compare' => 'equals',
				'value'   => 'html_block',
			),
		),
		'class'        => 'xts-html-block-links',
		'priority'     => 30,
	)
);

Options::add_field(
	array(
		'id'          => 'thank_you_page_default_content',
		'name'        => esc_html__( 'Default "Thank you page" content', 'xts-theme' ),
		'description' => esc_html__( 'If you use custom extra content you can disable default WooCommerce order details on the thank you page', 'xts-theme' ),
		'type'        => 'switcher',
		'section'     => 'thank_you_page_section',
		'default'     => '1',
		'priority'    => 40,
	)
);
