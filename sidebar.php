<?php 
if(is_front_page()) : ?>

			<ul class="xoxo">

<?php
	/* When we call the dynamic_sidebar() function, it'll spit out
	 * the widgets for that widget area. If it instead returns false,
	 * then the sidebar simply doesn't exist, so we'll hard-code in
	 * some default sidebar stuff just in case.
	 */
	if ( ! dynamic_sidebar( 'primary-widget-area' ) ) :
    if(function_exists('the_widget')):
      the_widget('Recent_Posts_With_Time', 
                  array('title'=>__('Últimas Notícias','pgsm-boilerplate-child'), 'number'=>'5'), 
                  array('widget_id'=>'ultimas-noticias-widget',
                        'before_widget' => '<li>',
                        'after_widget' => '</li>',
                        'before_title' => '<h3>',
                        'after_title' => '</h3>'));
    endif;
  endif;?>
			</ul>

<?php
	// A second sidebar for widgets, just because.
	if ( is_active_sidebar( 'secondary-widget-area' ) ) : ?>

			<ul class="xoxo">
				<?php dynamic_sidebar( 'secondary-widget-area' ); ?>
			</ul>

<?php endif; ?>
<?php endif; ?>