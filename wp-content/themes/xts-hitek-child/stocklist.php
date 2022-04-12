<?php
/*
  Template Name: Stocklist
*/


get_header();

xts_get_sidebar( 'sidebar-left' );

?>
<div class="xts-content-area<?php echo esc_attr( xts_get_content_classes() ); ?>">
	<?php
    $posts_per_page = 24;
    $paged = isset($_GET['start']) ? $_GET['start'] : 1;
    $filters = $_GET['filters'] ? $_GET['filters'] : [];

    $args = array(
      'post_type' => 'stocklist',
      'post_status' => 'publish',
      'posts_per_page' => $posts_per_page,
      'paged' => $paged,
    );

    if (count($filters) === 1) {
      $args['meta_key'] = array_keys($filters)[0];
      $args['meta_value'] = $filters[array_keys($filters)[0]];
    } else {
      $args['meta_query'] = array('relattion' => 'AND');
      $query = [];

      foreach($filters as $key => $value) {
        $query[] = array(
          'key'	 	=> $key,
          'value'	  	=> array($value),
          'compare' 	=> 'IN',
        );
      }

      array_push($args['meta_query'], $query);
    }

    $query = new WP_Query( $args );
    $found_posts = $query->found_posts;
    $total_page = ceil($found_posts / $posts_per_page);

    if ( $query->have_posts() ) :
  ?>
    <div class="xts-products products xts-row xts-row-lg-4 xts-row-md-3 xts-row-2 xts-row-spacing-20 xts-prod-design-icons-alt xts-cat-design-default xts-scheme-default-cat">
  <?php
      while ($query->have_posts() ) :
        $query->the_post();
  ?>
    <div class="xts-col" data-loop="1">
      <div
        class="xts-product product type-product post-3581 status-publish first instock product_cat-loai-tich-hop-bo-khuech-dai product_tag-cam-bien-omron product_tag-omron-vietnam has-post-thumbnail shipping-taxable product-type-simple"
      >
        <div class="xts-product-thumb">
          <a href="<?=get_post_permalink()?>" class="xts-product-link xts-fill"></a>
          <div class="xts-product-image">
            <?=get_the_post_thumbnail()?>
          </div>
        </div>
        <div class="xts-product-content">
          <h2 class="woocommerce-loop-product__title xts-entities-title">
            <a href="<?=get_post_permalink()?>">
            <?=get_the_title()?></a>
          </h2>

          <div class="xts-product-categories xts-product-meta">
          <a href="<?=get_post_permalink()?>">
            Model: <?php echo get_field( 'model' ); ?> </a>
            <a href="h<?=get_post_permalink()?>"
              rel="tag">Maker: <?php echo get_field( 'maker' ); ?></a>
              <a href="h<?=get_post_permalink()?>"
              rel="tag">Description: <?php echo get_field( 'description' ); ?></a>
          </div>

          <div class="xts-product-content-head">
          </div>

        </div>
      </div>
    </div>
  <?php
      endwhile;
  ?>
    </div>
  <?php
    endif;
	?>

  <?php
    if ( $total_page > 1 ) :
  ?>
    <div class="pagination">
      <?php
        for ( $i = 0 ; $i < $total_page ; $i++ ):
      ?>
        <a href="?start=<?= $i + 1 ?>"><?= $i+1 ?></a>
      <?php
        endfor;
      ?>
    </div>
  <?php
    endif;
  ?>
  <h1>Hello stocklist</h1>
</div>
<?php

xts_get_sidebar( 'sidebar-right' );

get_footer();
