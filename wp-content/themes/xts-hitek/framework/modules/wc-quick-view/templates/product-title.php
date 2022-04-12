<?php
/**
 * Quick view product title template
 *
 * @package xts
 * @version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'No direct script access allowed' );
}

?>

<h1 itemprop="name" class="product_title entry-title">
	<a href="<?php the_permalink(); ?>">
		<?php the_title(); ?>
	</a>
</h1>
