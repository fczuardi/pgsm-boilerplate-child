<?php
//require_once ( get_stylesheet_directory() . '/theme-options.php' );
define('HEADER_IMAGE', trailingslashit( get_stylesheet_directory_uri() ).'/images/headers/logo.png');
define('HEADER_IMAGE_WIDTH', 408);
define('HEADER_IMAGE_HEIGHT', 165);
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
  add_action('admin_menu', 'mt_add_pages');
  // autop is lame, remove it
  remove_filter('the_content', 'wpautop');
  remove_filter('the_excerpt', 'wpautop');
  add_action('init', 'create_type_disciplina');
  add_filter('the_editor_content', 'my_preset_content');
  add_filter('default_title', 'my_preset_title');
  
}
add_action( 'after_setup_theme', 'my_child_theme_setup' );

//CUSTOM POST TYPES
function create_type_disciplina() {
  $labels = array(
      'name' => _x('Disciplinas', 'post type general name', 'pgsm-boilerplate-child'),
      'singular_name' => _x('Disciplina', 'post type singular name', 'pgsm-boilerplate-child'),
      'add_new' => _x('Adicionar Nova', 'disciplina', 'pgsm-boilerplate-child'),
      'add_new_item' => __('Adicionar Nova Disciplina', 'pgsm-boilerplate-child'),
      'edit_item' => __('Editar Disciplina', 'pgsm-boilerplate-child'),
      'new_item' => __('Nova Disciplina', 'pgsm-boilerplate-child'),
      'view_item' => __('Visualizar Disciplina', 'pgsm-boilerplate-child'),
      'search_items' => __('Pesquisar Disciplinas', 'pgsm-boilerplate-child'),
      'not_found' =>  __('Nenhuma disciplica encontrada', 'pgsm-boilerplate-child'),
      'not_found_in_trash' => __('Não há disciplinas na lixeira', 'pgsm-boilerplate-child'), 
      'parent_item_colon' => '',
      'menu_name' => 'Disciplinas'
    );  
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true, 
      'show_in_menu' => true, 
      'query_var' => true,
      'rewrite' => array('slug' => 'disciplinas'),
      // 'rewrite' => true,
      'capability_type' => 'post',
      'has_archive' => true, 
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor','author')
      // 'supports' => array('title','editor','author','thumbnail','excerpt','comments')
    );
  register_post_type( 'pgsm_disciplina', $args);
}

function my_preset_title() {
  global $post;
  if ($_REQUEST['post_type'] == 'pgsm_disciplina' ) {
      $default_title = file_get_contents(get_stylesheet_directory() . '/custom_post_type_prefill/disciplina.title.html');
  } else {
      $default_title = '';
  }
  return $default_title;
}

function my_preset_content() {
    global $post;
    if ( $post->post_content == '' and $post->post_type == 'pgsm_disciplina' ) {
        $default_content = file_get_contents(get_stylesheet_directory() . '/custom_post_type_prefill/disciplina.content.html');
    } else {
        $default_content = $post->post_content;
    }
    return $default_content;
    // return $post->post_type;
}


// Código emprestado de http://codex.wordpress.org/Administration_Menus
function mt_add_pages() {
    add_pages_page(__('Conjunto Inicial','pgsm-boilerplate-child'), __('Conjunto Inicial','pgsm-boilerplate-child'), 'manage_options', 'startingpages', 'mt_settings_page');
}
// mt_settings_page() displays the page content for the Test settings submenu
function mt_settings_page() {
    echo "<h2>" . __( 'Default Static Pages', 'pgsm-boilerplate-child' ) . "</h2>";
    //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }
    $submit_label = __( 'Pupular Páginas Iniciais', 'pgsm-boilerplate-child' );
    $has_pages = false;
    $content_pages_path = '/pages_content/pt';
    //loop through the portuguese content file directory
    if ($handle = opendir(get_stylesheet_directory() . $content_pages_path)) {
        /* This is the correct way to loop over the directory. */
        while (false !== ($file = readdir($handle))) {
          $name_parts = explode('.', $file);
          $page = get_page_by_path($name_parts[1]);
          if ($name_parts[1] == '') {
            continue;
          }
          echo $name_parts[1] . '<br>';
          
          if ($_POST['update_pages'] == 'yes'){
            $page_content = file_get_contents(get_stylesheet_directory() . $content_pages_path . '/' . $file);

            //extract metadata
            preg_match("/^\<!--([^∫]*)-->/U", $page_content, $matches);
            $meta_lines = explode("\n", trim($matches[1]));
            $meta = array();
            foreach ($meta_lines as $line){
              preg_match("/([^∫]*)\s*\:\s*(.*)/", $line, $parts);
              $meta[strtolower($parts[1])] = $parts[2];
            }
            if (is_null($page)){
              $updated_page = array();
              $updated_page['post_type']  = 'page';
              $updated_page['post_name'] = $name_parts[1];
            } else {
              $updated_page = (array) $page;
              $has_pages = true;
            }
            $updated_page['post_title'] = $meta['title'];
            $updated_page['post_status'] = 'publish';
            $updated_page['post_content'] = substr($page_content,strlen($matches[0])+1);
            $updated_page['menu_order'] = (int) $name_parts[0]; //If new post is a page, sets the order should it appear in the tabs.
            //   'menu_order' => [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
            //   'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
            //   'ping_status' => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
            //   'pinged' => [ ? ] //?
            //   'post_author' => [ <user ID> ] //The user ID number of the author.
            //   'post_category' => [ array(<category id>, <...>) ] //Add some categories.
            //   'tags_input' => [ '<tag>, <tag>, <...>' ] //For tags.
            //   'post_parent' => [ <post ID> ] //Sets the parent of the new post.
            if (is_null($page)){
              $pageid = wp_insert_post ($updated_page);
              if ($pageid == 0) { 
               echo  'Add Page Failed <br>';
              }
            } else {
              $pageid = $updated_page['ID'];
              wp_update_post($updated_page);
            }
            if ($meta['template']) {
              update_post_meta($pageid, '_wp_page_template', $meta['template'] . '.php');
            }
            if ($meta['option']) {
              if (($meta['option'] == 'page_on_front') || ($meta['option'] == 'page_for_posts')){
                update_option( $meta['option'], $pageid );
              }
            }
          }
        } // while
        closedir($handle);
        if ($has_pages) {
          $submit_label = __( 'Sobrescrever Páginas Iniciais (cuidado!)', 'pgsm-boilerplate-child' );
        }
        ?>
        <form action="" method="POST">
          <input type="hidden" name="update_pages" value="yes">
          <input type="submit" value="<?php echo $submit_label;?>"/>
        <?php
    }    
}
/**
 * Prints HTML with meta information for the current post—date/time and author.
 *
 * @since Twenty Ten 1.0
 */
function boilerplate_posted_on() {
	printf( __( '<span class="%1$s">Posted on</span> %2$s <span class="meta-sep">by</span> %3$s', 'boilerplate' ),
		'meta-prep meta-prep-author',
		sprintf( '<a href="%1$s" title="%2$s" rel="bookmark"><span class="entry-date">%3$s</span></a>',
			get_permalink(),
			esc_attr( get_the_time('c') ),
			get_the_date('D j M Y : h\hi')
		),
		sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s">%3$s</a></span>',
			get_author_posts_url( get_the_author_meta( 'ID' ) ),
			sprintf( esc_attr__( 'View all posts by %s', 'boilerplate' ), get_the_author() ),
			get_the_author()
		)
	);
}


class Recent_Posts_With_Time extends WP_Widget_Recent_Posts {
  function Recent_Posts_With_Time() {
    $widget_ops = array('classname' => 'widget_recent_posts_with_time', 
                        'description' => __( "The most recent posts on your site, with timestamp") );
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

    $title = apply_filters('widget_title', empty($instance['title']) ? __('Últimas Notícias') : $instance['title'], $instance, $this->id_base);
    if ( ! $number = absint( $instance['number'] ) )
      $number = 10;

    $r = new WP_Query(array('posts_per_page' => $number, 'nopaging' => 0, 'post_status' => 'publish', 'ignore_sticky_posts' => true));
    if ($r->have_posts()) :
    ?>
    <?php echo $before_widget; ?>
    <?php if ( $title ) echo $before_title . $title . $after_title; ?>
    <a href="<?php bloginfo('rss2_url'); ?>" class="feed-icon">RSS</a>
    <ul>
    <?php  while ($r->have_posts()) : $r->the_post(); ?>
    <li><time pubdate datetime="<?php the_date('c');?>"><?php the_time('D j M Y : h\hi'); ?></time><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
    <?php endwhile; ?>
    </ul>
    <p>
      <a href="<?php echo get_permalink(get_option('page_for_posts'));?>"><?php _e('Ler todas as notícias', 'pgsm-boilerplate-child');?></a>
    </p>
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
  //Register the default widgets for this theme
  register_widget('Recent_Posts_With_Time');
  
  // Area 1, located in the header. Empty by default.
  register_sidebar( array(
    'name' => __( 'First Header Widget Area', 'pgsm-boilerplate-child' ),
    'id' => 'first-header-widget-area',
    'description' => __( 'The first header widget area', 'pgsm-boilerplate-child' ),
    'before_widget' => '<div id="%1$s" class="widget-container %2$s">',
    'after_widget' => '</div>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );
  // Area 2, located at the top of the sidebar on the home page.
  register_sidebar( array(
    'name' => __( 'Primary Widget Area', 'pgsm-boilerplate-child' ),
    'id' => 'primary-widget-area',
    'description' => __( 'The primary widget area', 'pgsm-boilerplate-child' ),
    'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
    'after_widget' => '</li>',
    'before_title' => '<h3 class="widget-title">',
    'after_title' => '</h3>',
  ) );

  
  // check to see if the primary widget area has the same six default widgets
  // (http://wordpress.org/support/topic/setting-default-widgets-in-twenty-ten-child-theme?replies=8#post-2167119)
  // of a fresh wordpress installation, if so, set the pgsm default primary widget area
  $default_wp_primary_widgets = array ( 0 => 'search-2', 
                                        1 => 'recent-posts-2', 
                                        2 => 'recent-comments-2', 
                                        3 => 'archives-2', 
                                        4 => 'categories-2', 
                                        5 => 'meta-2');
  $current_widgets = get_option('sidebars_widgets');
  if (count(array_diff($default_wp_primary_widgets, $current_widgets['primary-widget-area'])) == 0){
    $current_widgets['primary-widget-area'] = array ( 0 => 'recent-posts-with-time-2');
    $current_widgets['first-header-widget-area'] = array ( 0 => 'qtranslate-5');
    update_option( 'sidebars_widgets',  $current_widgets);
  }
}

?>