<?php
/**
 * Child theme functions file.
 *
 * @package xts
 */

/**
 * Enqueue script and styles for child theme.
 */
 
function xts_child_enqueue_styles() {
	wp_enqueue_style( 'xts-child-style', get_stylesheet_directory_uri() . '/style.css', array( 'xts-style' ), XTS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'xts_child_enqueue_styles', 200 );

// if ( !function_exists( 'is_product_category' ) ) { 
//     require_once (WP_PLUGIN_DIR .'/woocommerce/includes/wc-conditional-functions.php'); 
// }

function _additional_woo_query( $query ) {
    // ?filters[maker]=airtac&filter[description]=Bearing&filters[model]=NZZZ
    // ?keyword=bear&filter=description
    
    if (is_product_category()) {
        $term = get_queried_object();
        //var_dump($term);
        $stockListVal = get_term_meta( $term->term_id, $key = 'stocklist_page', true );
        if ($stockListVal == 1) {
            $filters = isset($_GET['filters']) ? $_GET['filters'] : [];
            $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
            $filter = isset($_GET['filter']) ? $_GET['filter'] : null;

            if (!empty($keyword)) { //var_dump(123);
                $myQuery[] = array(
                   // 'key'	 	=> $filter,
                    'value'	  	=> $keyword,
                    'compare' 	=> 'LIKE',
                    
                );

                $myQuery[] = array('tax_query' => array(
                    // 'relattion' => 'OR',
                    array(
                        'taxonomy' => 'product_brand',
                        'compare' 	=> 'LIKE',
                        'field' => 'name',
                        'terms' => $keyword,
                )));
                
                        $query->set( 'meta_query', $myQuery );
                // if ($filter !== 'maker') {
                //     $myQuery[] = array(
                //         'key'	 	=> $filter,
                //         'value'	  	=> $keyword,
                //         'compare' 	=> 'LIKE',
                //     );
                //     $query->set( 'meta_query', $myQuery );
                // } else {
                //     $query->set( 'tax_query', array(
                //         'relattion' => 'AND',
                //         array(
                //             'taxonomy' => 'product_brand',
                //             'terms' => $keyword,
                //         )
                //     ) );
                // }
            }

            // if (count($filters) === 1) {
            //     $meta_key = array_keys($filters)[0];
            //     $meta_value = $filters[array_keys($filters)[0]];
            //     switch ($meta_key) {
            //         case 'description':
            //             case 'model':
            //             $query->set( 'meta_key', $meta_key );
            //             $query->set( 'meta_value', $meta_value);
            //             break;

            //         case 'maker':
            //             $query->set( 'tax_query', array(
            //                 array(
            //                     'taxonomy' => 'product_brand',
            //                     'field' => 'slug',
            //                     'terms' => $meta_value
            //                 )
            //             ) );
            //             break;

            //         default:
            //             # code...
            //             break;
            //     }
                // if ($meta_key !== 'maker') {
                //     $query->set( 'meta_key', $meta_key );
                //     $query->set( 'meta_value', $meta_value);
                // } else {
                //     if ($meta_key === 'model') {
                //         var_dump(123);
                //         var_dump($meta_key);

                //     } else {
                //         $query->set( 'tax_query', array(
                //             array(
                //                 'taxonomy' => 'product_brand',
                //                 'field' => 'slug',
                //                 'terms' => $meta_value
                //             )
                //         ) );
                //     }
                // }
            // } else {
            //     foreach($filters as $key => $value) {
            //         $myQuery = array('relattion' => 'AND');
            //         if ($key !== 'maker') {
            //             $myQuery[] = array(
            //                 'key'	 	=> $key,
            //                 'value'	  	=> array($value),
            //                 'compare' 	=> 'IN',
            //             );
            //         } else {
            //             // $query->set( 'meta_query', $myQuery );
            //            if ($key === 'maker') {
            //                 $query->set( 'tax_query', array(
            //                     'relattion' => 'AND',
            //                     array(
            //                         'taxonomy' => 'product_brand',
            //                         'terms' => $value,
            //                     )
            //                 ) );
            //            }
            //         }
            //     }
            //     $query->set( 'meta_query', $myQuery );
            // }
        }

    }
}

// alter query vao trang danh muc
add_action( 'pre_get_posts', '_additional_woo_query' );

// bo add to cart
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart');
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );

/** Dich tieng viet */
function ra_change_translate_text( $translated_text ) {
if ( $translated_text == 'Old Text' ) {
$translated_text = 'New Translation';
}
return $translated_text;
}
add_filter( 'gettext', 'ra_change_translate_text', 20 );
function ra_change_translate_text_multiple( $translated ) {
$text = array(
'Mô tả' => 'Mô tả sản phẩm',
'Oops! That page can’t be found' => 'Lỗi 404! Trang web không tồn tại',
'It looks like nothing was found at this location. Maybe try one of the links below or a search'=> 'Chúng tôi không tìm thấy trang này trên hệ thống, vui lòng thử chức năng tìm kiếm bên dưới',
'Leave a comment' => 'Viết bình luận',
'Continue reading' => 'Đọc tiếp',
'View more' => 'Xem thêm',
'Category Archives' => 'Danh mục',
'Posted in' => 'Đăng tại',
'POSTED ON' => 'Đăng ngày',
'SHOPPING CART' => 'Giỏ hàng',
'CHECKOUT DETAILS' => 'Thông tin thanh toán',
'ORDER COMPLETE' => 'Hoàn tất đặt hàng',
'CATEGORY ARCHIVES' => 'Chuyên mục',
'MY ACCOUNT'=> 'Tài khoản của tôi',
'POSTED ON'=> 'Đăng lúc',
'BY'=> 'bởi',
'Load more'=> 'Xem thêm',
'Loading'=> 'Đang tải',
);
$translated = str_ireplace( array_keys($text), $text, $translated );
return $translated;
}
add_filter( 'gettext', 'ra_change_translate_text_multiple', 20 );


function wpb_custom_new_menu() {
  register_nav_menu('header-categories',__( 'Header Categories' ));
}
add_action( 'init', 'wpb_custom_new_menu' );

/**
 * Add custom sorting options (asc/desc)
 */
add_filter( 'woocommerce_product_subcategories_args', 'custom_woocommerce_get_subcategories_ordering_args' );

    function custom_woocommerce_get_subcategories_ordering_args( $args ) {
      $args['orderby'] = 'id';
      return $args;
    }