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
  //custom types
  add_action('init', 'create_type_disciplina');
  add_action('init', 'create_type_orientador');
  //customizacoes para os custom types
  add_filter('request', 'filter_request_for_custom_types_categories');
  add_filter('request', 'filter_pagination_for_custom_types');
  add_filter('the_editor_content', 'my_preset_content');
  add_filter('default_title', 'my_preset_title');
  //custom meta boxes for custom post types
  add_action( 'add_meta_boxes', 'add_orientador_custom_boxes' );
  add_action( 'save_post', 'orientador_save_postdata' );
  //custom user fields
  add_action( 'show_user_profile', 'extra_profile_fields' );
  add_action( 'edit_user_profile', 'extra_profile_fields' );
  add_action( 'personal_options_update', 'save_extra_profile_fields' );
  add_action( 'edit_user_profile_update', 'save_extra_profile_fields' );
  //custom style sheet for admin pages
  add_action('admin_head', 'my_admin_head');
}
add_action( 'after_setup_theme', 'my_child_theme_setup' );

function my_admin_head() {
  echo '<link rel="stylesheet" type="text/css" href="' . trailingslashit( get_stylesheet_directory_uri() ).'/wp-admin.css' . '">';
}

function extra_profile_fields( $user ) {?>

<h3>Links relacionados</h3>

	<table class="form-table">
		<tr>
			<th><label for="url_lattes">Currículo Lattes</label></th>
			<td>
				<input type="text" name="url_lattes" id="url_lattes" value="<?php echo esc_attr( get_the_author_meta( 'url_lattes', $user->ID ) ); ?>" class="url-field" /><br />
				<span class="description">Exemplo: http://lattes.cnpq.br/2484665702538194 </span>
			</td>
		</tr>
		<tr>
			<th><label for="url_pubmed">Pubmed</label></th>
			<td>
				<input type="text" name="url_pubmed" id="url_pubmed" value="<?php echo esc_attr( get_the_author_meta( 'url_pubmed', $user->ID ) ); ?>" class="url-field" /><br />
				<span class="description">Exemplo: http://www.ncbi.nlm.nih.gov/pubmed?term=%22Zuardi%20AW%22%5BAuthor%5D </span>
			</td>
		</tr>
		<tr>
			<th><label for="url_twitter">Twitter</label></th>
			<td>
				<input type="text" name="url_twitter" id="url_twitter" value="<?php echo esc_attr( get_the_author_meta( 'url_twitter', $user->ID ) ); ?>" class="url-field" /><br />
				<span class="description">Exemplo: http://twitter.com/seu_username</span>
			</td>
		</tr>
	</table>
<h4>Outros Links Relacionados</h4>
<p><?php 
$other_links_value = get_the_author_meta( 'other_links', $user->ID );
if ($other_links_value){
  $other_links = json_decode($other_links_value, true);
}
?></p>
<table>
  <thead>
    <td>Nome do site</td>
    <td>Endereço</td>
  </thead>
  <tbody>
    <?php
    for ($i=0; $i<10; $i++){
      ?>
      <tr>
        <td><input type="text" name="related_link_names[<?php echo $i; ?>]" value="<?php echo $other_links[$i][0]; ?>" /></td>
        <td><input type="text" name="related_links[<?php echo $i; ?>]" class="url-field" value="<?php echo $other_links[$i][1]; ?>" /></td>
      </tr>
      <?php
    }
    ?>
  </tbody>
</table>
<?php
}

function save_extra_profile_fields( $user_id ) {
  if ( !current_user_can( 'edit_user', $user_id ) )
    return false;
  foreach(array('url_lattes', 'url_pubmed', 'url_twitter') as $field_name){
    if ( $_POST[$field_name] ){
      update_usermeta( $user_id, $field_name, $_POST[$field_name] );
    }
  }
  if ($_POST['related_link_names']){
    $related_links_json = "[";
    $links = array();
    foreach ($_POST['related_link_names'] as $index=>$site_name){
      if ($_POST['related_links'][$index]){
        $links[] = '["' . $site_name . '", "' . $_POST['related_links'][$index] . '"]';
      }
    }
    $related_links_json .= implode(', ', $links) . "]";
    update_usermeta( $user_id, 'other_links', $related_links_json );
  }
}

function add_orientador_custom_boxes() {
  add_meta_box( 
    'orientador_condicao',
    __( 'Condição', 'tipo de orientador', 'pgsm-boilerplate-child' ),
    'orientador_meta_box_condicao',
    'pgsm_orientador', 'side', 'high' 
  );
  add_meta_box( 
    'orientador_titulo',
    __( 'Prefixo', 'titulo acadêmico', 'pgsm-boilerplate-child' ),
    'orientador_meta_box_prefixo',
    'pgsm_orientador', 'side', 'high' 
  );
  add_meta_box( 
    'orientador_username',
    __( 'Conta do usuário', 'wordpress user account', 'pgsm-boilerplate-child' ),
    'orientador_meta_box_username',
    'pgsm_orientador', 'side', 'high' 
  );
}

function orientador_meta_box_condicao( $post ) {
  wp_nonce_field( plugin_basename( __FILE__ ), 'pgsm-boilerplate-child-nonce' );
  $condicao = get_post_meta( $post->ID, '_condicao', TRUE);
  if (!$condicao) $condicao = 'credenciado';
  ?>
  
  <label class="radio-label" for="condicao_credenciado">
  <input type="radio" id="condicao_credenciado" name="_condicao" value="credenciado" <?php if ($condicao == 'credenciado') echo "checked=1";?> />
  <?php _e("Credenciado", 'pgsm-boilerplate-child' ); ?>
  </label>

  <label class="radio-label" for="condicao_ex_orientador">
  <input type="radio" id="condicao_ex_orientador" name="_condicao" value="ex_orientador" <?php if ($condicao == 'ex_orientador') echo "checked=1";?> />
  <?php _e("Ex-orientador", 'pgsm-boilerplate-child' ); ?>
  </label>
  <?php
}

function orientador_meta_box_prefixo( $post ) {
  // Use nonce for verification
  wp_nonce_field( plugin_basename( __FILE__ ), 'pgsm-boilerplate-child-nonce' );

  $prefixo = get_post_meta( $post->ID, '_prefixo', TRUE);
  if (!$prefixo) $prefixo = 'Prof. Dr.';
  ?>
  
  <label class="radio-label" for="graduacao_academica_dr">
  <input type="radio" id="graduacao_academica_dr" name="_prefixo" value="Prof. Dr." <?php if ($prefixo == 'Prof. Dr.') echo "checked=1";?> />
  <?php _e("Prof. Dr.", 'pgsm-boilerplate-child' ); ?>
  </label>

  <label class="radio-label" for="graduacao_academica_dra">
  <input type="radio" id="graduacao_academica_dra" name="_prefixo" value="Profa. Dra." <?php if ($prefixo == 'Profa. Dra.') echo "checked=1";?> />
  <?php _e("Profa. Dra.", 'pgsm-boilerplate-child' ); ?>
  </label>

  <label class="radio-label" for="graduacao_academica_prof">
  <input type="radio" id="graduacao_academica_prof" name="_prefixo" value="Prof." <?php if ($prefixo == 'Prof.') echo "checked=1";?> />
  <?php _e("Prof.", 'pgsm-boilerplate-child' ); ?>
  </label>

  <label class="radio-label" for="graduacao_academica_profa">
  <input type="radio" id="graduacao_academica_profa" name="_prefixo" value="Profa." <?php if ($prefixo == 'Profa.') echo "checked=1";?> />
  <?php _e("Profa.", 'pgsm-boilerplate-child' ); ?>
  </label>
  <?php
}

function orientador_meta_box_username( $post ){
  wp_nonce_field( plugin_basename( __FILE__ ), 'pgsm-boilerplate-child-nonce' );
  $user_id = get_post_meta( $post->ID, '_user_id', TRUE);
  if (!$user_id) $user_id = '-1'; 
  $args = array(
      'show_option_all'         => null, // string
      'show_option_none'        => 'Nenhum', // string
      'hide_if_only_one_author' => null, // string
      'orderby'                 => 'display_name',
      'order'                   => 'ASC',
      'include'                 => null, // string
      'exclude'                 => null, // string
      'multi'                   => false,
      'show'                    => 'display_name',
      'echo'                    => true,
      'selected'                => $user_id,
      'include_selected'        => true,
      'name'                    => '_user_id', // string
      'id'                      => null, // integer
      'class'                   => null, // string 
      'blog_id'                 => $GLOBALS['blog_id'],
      'who'                     => null // string
  );
  wp_dropdown_users( $args );
  ?>
  <p>Caso o(a) orientador(a) ainda não se encontre na lista acima, <a href="/wp-admin/user-new.php">crie um novo usuário para ele(a)</a> e depois retorne aqui.</p>
  <?php
}


/* When the post is saved, saves our custom data */
function orientador_save_postdata( $post_id ) {
  // verify if this is an auto save routine. 
  // If it is our form has not been submitted, so we dont want to do anything
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return;

  // verify this came from the our screen and with proper authorization,
  // because save_post can be triggered at other times

  if ( !wp_verify_nonce( $_POST['pgsm-boilerplate-child-nonce'], plugin_basename( __FILE__ ) ) )
      return;

  
  // Check permissions
  if ( 'pgsm_orientador' == $_POST['post_type'] ) 
  {
    if ( !current_user_can( 'edit_orientador', $post_id ) )
        return;
  }

  // OK, we're authenticated: we need to find and save the data

  if ($_POST['_prefixo']){
    update_post_meta($post_id, '_prefixo', $_POST['_prefixo']);
  }
  if ($_POST['_condicao']){
    update_post_meta($post_id, '_condicao', $_POST['_condicao']);
  }
  if ($_POST['_user_id']){
    update_post_meta($post_id, '_user_id', $_POST['_user_id']);
  }
}


//CUSTOM POST TYPES
function create_type_orientador() {
  $capability_type_names = array('orientador', 'orientadores');
  $labels = array(
      'name' => _x('Orientadores', 'post type general name', 'pgsm-boilerplate-child'),
      'singular_name' => _x('Orientador', 'post type singular name', 'pgsm-boilerplate-child'),
      'add_new' => _x('Adicionar Novo', 'orientador', 'pgsm-boilerplate-child'),
      'add_new_item' => __('Adicionar Novo Orientador', 'pgsm-boilerplate-child'),
      'edit_item' => __('Editar Orientador', 'pgsm-boilerplate-child'),
      'new_item' => __('Novo Orientador', 'pgsm-boilerplate-child'),
      'view_item' => __('Visualizar Orientador', 'pgsm-boilerplate-child'),
      'search_items' => __('Pesquisar Orientadores', 'pgsm-boilerplate-child'),
      'not_found' =>  __('Nenhum orientador encontrado', 'pgsm-boilerplate-child'),
      'not_found_in_trash' => __('Não há orientadores na lixeira', 'pgsm-boilerplate-child'), 
      'parent_item_colon' => '',
      'menu_name' => 'Orientadores'
    );  
  $args = array(
      'labels' => $labels,
      'public' => true,
      'publicly_queryable' => true,
      'show_ui' => true, 
      'show_in_menu' => true, 
      'query_var' => true,
      'rewrite' => array('slug' => 'orientadores'),
      'capability_type' => $capability_type_names,
      'has_archive' => false, //é false mesmo, nao questione.
      'hierarchical' => false,
      'menu_position' => null,
      // 'taxonomies' => array('category'),
      'supports' => array('title','editor','author', 'custom-fields')
      // 'supports' => array('title','editor','author','thumbnail','excerpt','comments')
    );
  register_post_type( 'pgsm_orientador', $args);
  flush_rewrite_rules();
  //add all capabilities to the admin role
  $role = get_role( 'administrator' );
  $role->add_cap( 'edit_' . $capability_type_names[0] );
  $role->add_cap( 'edit_' . $capability_type_names[1] );
  $role->add_cap( 'edit_other_' . $capability_type_names[1] );
  $role->add_cap( 'publish_' . $capability_type_names[1] );
  $role->add_cap( 'read_' . $capability_type_names[0] );
  $role->add_cap( 'read_' . $capability_type_names[1] );
  $role->add_cap( 'read_private_' . $capability_type_names[1] );
  $role->add_cap( 'delete_' . $capability_type_names[0] );
}
function create_type_disciplina() {
  $capability_type_names = array('disciplina', 'disciplinas');
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
      'capability_type' => $capability_type_names,
      'has_archive' => false, //é false mesmo, nao questione.
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title','editor','author')
      // 'supports' => array('title','editor','author','thumbnail','excerpt','comments')
    );
  register_post_type( 'pgsm_disciplina', $args);
  flush_rewrite_rules();
  //add all capabilities to the admin role
  $role = get_role( 'administrator' );
  $role->add_cap( 'edit_' . $capability_type_names[0] );
  $role->add_cap( 'edit_' . $capability_type_names[1] );
  $role->add_cap( 'edit_other_' . $capability_type_names[1] );
  $role->add_cap( 'publish_' . $capability_type_names[1] );
  $role->add_cap( 'read_' . $capability_type_names[0] );
  $role->add_cap( 'read_' . $capability_type_names[1] );
  $role->add_cap( 'read_private_' . $capability_type_names[1] );
  $role->add_cap( 'delete_' . $capability_type_names[0] );
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
function filter_request_for_custom_types_categories($query_string){
  if (($query_string['pgsm_orientador'] == 'credenciados') || ($query_string['pgsm_orientador'] == 'ex_orientadores')) {
    $query_string['pagename'] = 'orientadores/' . $query_string['pgsm_orientador'];
    unset($query_string['post_type']);
    unset($query_string['name']);
  }
  return $query_string;
}

/*

O jeito que eu estou usando custom types estava conflitando com a opção do
wordpress gerenciar páginas de arquivos de custom type posts. Então a solução que
eu arranjei foi:

1- definir os tipos customizados usando o parametro 'has_archive' => false
2- fazer uma página (post tipo page) para ser a "home do post daquele tipo"
3- esta pagina segue um template que é o que cuida da query correta para listagem e paginacao
4- finalmente, eu coloco esta função abaixo para interceptar a chamada para uma url tipo
/disciplinas/page/2, que normalmente seria interpretada como "uma disciplina de slug page"
o que é errado, e reescrevo a querystring para que a paginacao seja repassada para
a "home do custom type" que ja está preparada para saber o que fazer

Solução inspirada em http://barefootdevelopment.blogspot.com/2007/11/fix-for-wordpress-paging-problem.html
*/
function filter_pagination_for_custom_types($query_string){
  if ($query_string['pgsm_disciplina'] == 'page'){
    $paged = str_replace('/', '', $query_string['page']);
    // replace querystring
    $query_string = array(
      'paged' => $paged,
      'pagename' => 'disciplinas'
    );
  }
    return $query_string;
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
        $page_ids = array();
        /* This is the correct way to loop over the directory. */
        while (false !== ($file = readdir($handle))) {
          $name_parts = explode('.', $file);
          $page_path = implode('/', array_slice($name_parts, 1, -1));
          $page = get_page_by_path($page_path);
          if ($name_parts[1] == '') {
            continue;
          }
          echo $page_path . '<br>';
          
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
              $updated_page['post_name'] = $name_parts[count($name_parts)-2];
            } else {
              $updated_page = (array) $page;
              $has_pages = true;
            }
            $updated_page['post_title'] = $meta['title'];
            $updated_page['post_status'] = 'publish';
            $updated_page['post_content'] = substr($page_content,strlen($matches[0])+1);
            $updated_page['menu_order'] = (int) $name_parts[0]; //If new post is a page, sets the order should it appear in the tabs.
            if($meta['parent']){
              // this will only work if the menu_order for the child comes after the parent
              // I am doing this to prevent an extra query to get the id of a page from the slug
              var_dump($page_ids);
              echo '<br>---<br>';
              $parentId = $page_ids[$meta['parent']];
              $updated_page['post_parent'] = $parentId; //Sets the parent of the new post.
            }
            //   'menu_order' => [ <order> ] //If new post is a page, sets the order should it appear in the tabs.
            //   'comment_status' => [ 'closed' | 'open' ] // 'closed' means no comments.
            //   'ping_status' => [ 'closed' | 'open' ] // 'closed' means pingbacks or trackbacks turned off
            //   'pinged' => [ ? ] //?
            //   'post_author' => [ <user ID> ] //The user ID number of the author.
            //   'post_category' => [ array(<category id>, <...>) ] //Add some categories.
            //   'tags_input' => [ '<tag>, <tag>, <...>' ] //For tags.
            if (is_null($page)){
              echo 'insert<br>';
              $pageid = wp_insert_post ($updated_page);
              if ($pageid == 0) { 
               echo  'Add Page Failed <br>';
              }
            } else {
              echo 'update<br>';
              $pageid = $updated_page['ID'];
              wp_update_post($updated_page);
            }
            $page_ids[$page_path] = $pageid;
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