<?php
/*
Template Name: Listagem de Disciplinas
*/
get_header(); ?>
<h1 class="entry-title com-subtitulo">Disciplinas</h1>
<h2>Lista Completa</h2>
<form action="<?php echo home_url( '/' ); ?>" id="searchform" method="get" role="search">
		<div>
		<input type="search" id="s" name="s" value="" placeholder="Pesquisar por...">
		<input type="submit" value="Pesquisar" id="searchsubmit">
    <input type="hidden" name="post_type" value="pgsm_disciplina" />
		</div>
</form>
<div class="layer-shadow"><hr /></div>

<?php
$post_per_page = -1;
$do_not_show_stickies = 1;
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$args=array(
    'paged' => $paged,
    'post_type' => 'pgsm_disciplina',
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
  	    <div <?php post_class('collapsible closed') ?> id="post-<?php the_ID(); ?>">
          <h2 class="colapse-toggle"><a class="colapse-toggle" href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
          <div class="layer-shadow light colapse-toggle"><hr /></div>
          <div class="entry">
            <?php the_content('Read the rest of this entry »'); ?>
          </div>
        </div>
        <div class="layer-shadow"><hr /></div>
      <?php endwhile; ?>
      <div class="navigation">
        <div class="line-button"><?php next_posts_link('Mais Disciplinas') ?></div>
      </div>
    <?php else : ?>
  		<h2 class="center">Not Found</h2>
  		<p class="center">Sorry, but you are looking for something that isn't here.</p>
  		<?php get_search_form(); ?>
  	<?php endif; 
    $wp_query = $temp;  //reset back to original query
?>

<?php get_footer(); ?>