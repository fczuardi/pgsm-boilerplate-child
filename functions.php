<?php
define('HEADER_IMAGE_WIDTH', 408);
define('HEADER_IMAGE_HEIGHT', 165);
add_action( 'after_setup_theme', 'my_child_theme_setup' );
function my_child_theme_setup() {
  register_default_headers( array(
    'pgsm' => array(
    'url' => trailingslashit( get_stylesheet_directory_uri() ).'/images/headers/logo.png',
    'thumbnail_url' => trailingslashit( get_stylesheet_directory_uri() ).'/images/headers/logo-thumbnail.png',
    'description' => __( 'PGSM', 'pgsm-boilerplate-child' )
    )
  ) );
  remove_action( 'widgets_init', 'boilerplate_widgets_init' );
  add_action('widgets_init', 'pgsm_widgets_init');
}

class Recent_Posts_With_Time extends WP_Widget_Recent_Posts {
  function Recent_Posts_With_Time() {
    $widget_ops = array('classname' => 'widget_recent_posts_with_time', 'description' => __( "The most recent posts on your site, with timestamp") );
    $this->WP_Widget('recent-posts-with-time', __('Recent Posts With Time'), $widget_ops);
    $this->alt_option_name = 'widget_recent_entries';
    add_action( 'save_post', array(&$this, 'flush_widget_cache') );
    add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
    add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
  }

  function widget($args, $instance) {
    $cache = wp_cache_get('widget_recent_posts', 'widget');

    if ( !is_array($cache) )
      $cache = array();

    if ( isset($cache[$args['widget_id']]) ) {
      echo $cache[$args['widget_id']];
      return;
    }

    ob_start();
    extract($args);

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Posts') : $instance['title'], $instance, $this->id_base);
    if ( ! $number = absint( $instance['number'] ) )
      $number = 10;

    $r = new WP_Query(array('posts_per_page' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => true));
    if ($r->have_posts()) :
    ?>
    <?php echo $before_widget; ?>
    <?php if ( $title ) echo $before_title . $title . $after_title; ?>
    <ul>
    <?php  while ($r->have_posts()) : $r->the_post(); ?>
    <li><?php the_date('c', '<time pubdate datetime="', '">'.get_the_date().'</time>'); ?><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
    <?php endwhile; ?>
    </ul>
    <?php echo $after_widget; ?>
    <?php
    // Reset the global $the_post as this query will have stomped on it
    wp_reset_postdata();

    endif;

    $cache[$args['widget_id']] = ob_get_flush();
    wp_cache_set('widget_recent_posts', $cache, 'widget');
  }
  
}

function pgsm_widgets_init() {
  // Remove all default widgets (listed on /wp-includes/default-widgets.php)
  // unregister_widget('WP_Widget_Pages');
  // unregister_widget('WP_Widget_Calendar');
  // unregister_widget('WP_Widget_Archives');
  // unregister_widget('WP_Widget_Links');
  // unregister_widget('WP_Widget_Meta');
  // unregister_widget('WP_Widget_Search');
  // unregister_widget('WP_Widget_Text');
  // unregister_widget('WP_Widget_Categories');
  // unregister_widget('WP_Widget_Recent_Posts');
  // unregister_widget('WP_Widget_Recent_Comments');
  // unregister_widget('WP_Widget_RSS');
  // unregister_widget('WP_Widget_Tag_Cloud');
  // unregister_widget('WP_Nav_Menu_Widget');
  
  //Register the default widgets for this theme
  register_widget('Recent_Posts_With_Time');
  
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Primary Widget Area', 'boilerplate' ),
		'id' => 'primary-widget-area',
		'description' => __( 'The primary widget area', 'boilerplate' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
  
}

?>