<?php
/*
Template Name: Página de Notícias

 ESTA NÃO É A HOME PAGE!
 APESAR DE SE CHAMAR INDEX.PHP ISTO AQUI É O TEMPLATE DA PÁGINA DE NOTÍCIAS
 
Baseado no exemplo: http://codex.wordpress.org/Pages#A_Page_of_Posts
*/
get_header(); ?>
<h1 class="entry-title">Notícias</h1>
<?php get_search_form( true ); ?>
<div class="layer-shadow"><hr /></div>
<?php
$post_per_page = 2;
$do_not_show_stickies = 1;
$args=array(
    // 'category__in' => array($cat),
    'orderby' => 'date',
    'order' => 'DESC',
    'paged' => $paged,
    'posts_per_page' => $post_per_page,
    'caller_get_posts' => $do_not_show_stickies
  );
  $temp = $wp_query;  // assign orginal query to temp variable for later use   
  $wp_query = null;
  $wp_query = new WP_Query($args);
  if( have_posts() ) : 
  		while ($wp_query->have_posts()) : $wp_query->the_post(); ?>
  	    <div <?php post_class() ?> id="post-<?php the_ID(); ?>">
          <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
          <p><time pubdate datetime="<?php the_date('c');?>"><?php the_time('D j M Y : h\hi'); ?></time> : <span class="publicado-por"><?php echo _e('Publicado por', 'pgsm-boilerplate-child'); ?> <?php the_author() ?></span></p>
          <div class="layer-shadow light"><hr /></div>
          <div class="entry">
            <?php the_content('Read the rest of this entry »'); ?>
          </div>        </div>
        <div class="layer-shadow"><hr /></div>
      <?php endwhile; ?>
      <div class="navigation">
        <div class="line-button"><?php next_posts_link('Mais Notícias') ?></div>
      </div>
    <?php else : ?>
  		<h2 class="center">Not Found</h2>
  		<p class="center">Sorry, but you are looking for something that isn't here.</p>
  		<?php get_search_form(); ?>
  	<?php endif; 
  	$wp_query = $temp;  //reset back to original query
?>

<?php get_footer(); ?>