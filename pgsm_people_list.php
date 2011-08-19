<?php
global $page_title, $page_subtitle, $next_page_label, $list_item_type, $post_subtype, $post_per_page;
get_header(); 
?>
<h1 class="entry-title"><?php echo $page_title; ?></h1>
<h2><?php echo $page_subtitle; ?></h2>
<form action="<?php echo home_url( '/' ); ?>" id="searchform" method="get" role="search">
		<div>
		<input type="search" id="s" name="s" value="" placeholder="Pesquisar por...">
		<input type="submit" value="Pesquisar" id="searchsubmit">
		<input type="hidden" name="post_type" value="<?php echo $list_item_type; ?>" />
    <input type="hidden" name="meta_key" value="_condicao" />
    <input type="hidden" name="meta_value" value="<?php echo $post_subtype; ?>" />
		</div>
</form>
<div class="layer-shadow"><hr /></div>

<?php
$do_not_show_stickies = 1;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args=array(
    'paged' => $paged,
    'post_type' => $list_item_type,
    'meta_key' => '_condicao', 
    'meta_value' => $post_subtype,
    'orderby' => 'title',
    'order' => 'ASC',
    'posts_per_page' => $post_per_page,
    'caller_get_posts' => $do_not_show_stickies
  );
  $temp = $wp_query;  // assign orginal query to temp variable for later use   
  $wp_query = null;
  $wp_query = new WP_Query($args);
  if( have_posts() ) : 
  		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
  	    <div <?php post_class('collapsible closed people') ?> id="post-<?php the_ID(); ?>">
          <h2 class="colapse-toggle"><a class="colapse-toggle" 
            href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php 
            the_title(get_post_meta( $post->ID, '_prefixo', TRUE).' '); ?></a></h2>
          <div class="layer-shadow light colapse-toggle"><hr /></div>
          <aside class="sidebar">
            <?php
            $user_id = get_post_meta($post->ID, '_user_id', true);
            if (($user_id) && ($user_id != -1)){
              $pessoa = get_userdata($user_id);
              // Foto da pessoa
              if (userphoto_exists($pessoa)){
                userphoto($pessoa);
              }
              // Posts da pessoa
              $professor_args=array(
                  'orderby' => 'date',
                  'order' => 'DESC',
                  'author' => $user_id,
                  'posts_per_page' => $post_per_page,
                  'caller_get_posts' => $do_not_show_stickies
                );
              $posts_by_author_query = new WP_Query($professor_args);
              if ( $posts_by_author_query->have_posts() ) { ?>
                <h2><?php _e('Últimos Posts', 'pgsm-boilerplate-child');?></h2>
                <a href="<?php echo bloginfo( 'url' ) . '/author/' . $pessoa->user_login . '/feed/' ?>" class="feed-icon">RSS</a>
                <ol>
                <?php
                while ( $posts_by_author_query->have_posts() ) : $posts_by_author_query->the_post(); ?>
                  <li>
                    <time pubdate datetime="<?php the_date('c');?>"><?php the_time('D j M Y : h\hi'); ?></time>
                    <a href="<?php the_permalink() ?>" title="<?php 
                    echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); 
                    ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a>
                  </li>
                <?php 
                endwhile; ?>
                </ol>
                <p>
                  <a href="<?php echo get_permalink(get_option('page_for_posts')) . '?author=' . $user_id;?>"><?php _e('Ler todos os posts', 'pgsm-boilerplate-child');?></a>
                </p>
                <?php
              }
              wp_reset_postdata();
              
              // Links relacionados da pessoa
              $links_relacionados = array();
              if ($pessoa->url_lattes) { 
                $links_relacionados[] = array(__('Currículo Lattes', 'pgsm-boilerplate-child'), $pessoa->url_lattes);
              }
              if ($pessoa->url_pubmed) { 
                $links_relacionados[] = array(__('Pubmed', 'pgsm-boilerplate-child'), $pessoa->url_pubmed);
              }
              if ($pessoa->url_twitter) { 
                $links_relacionados[] = array(__('Twitter', 'pgsm-boilerplate-child'), $pessoa->url_twitter);
              }
              if ($pessoa->other_links) { 
                $links_relacionados = array_merge($links_relacionados, json_decode($pessoa->other_links, true));
              }
              if (count($links_relacionados) > 0){?>
                <h2><?php _e('Links Relacionados', 'pgsm-boilerplate-child');?></h2>
                <ul>
                <?php
                for ($i=0; $i < count($links_relacionados); $i++){
                  $link_name = $links_relacionados[$i][0];
                  $link_url = $links_relacionados[$i][1]; ?>
                  <li>
                    <a href="<?php echo $link_url; ?>"><?php echo $link_name; ?></a>
                  </li>
                  <?php
                } ?>
                </ul>
                <?php
              }
            } ?>
          </aside>
          <div class="entry">
            <?php the_content('Read the rest of this entry »'); ?>
          </div>
        </div>
        <div class="layer-shadow"><hr /></div>
      <?php endwhile; ?>
      <div class="navigation">
        <div class="line-button"><?php next_posts_link($next_page_label) ?></div>
      </div>
    <?php else : ?>
  		<h2 class="center">Not Found</h2>
  		<p class="center">Sorry, but you are looking for something that isn't here.</p>
  		<?php get_search_form(); ?>
  	<?php endif; 
    $wp_query = $temp;  //reset back to original query
    get_footer(); 
?>