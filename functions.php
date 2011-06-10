<?php
define('HEADER_IMAGE_WIDTH', 408);
define('HEADER_IMAGE_HEIGHT', 165);
add_action( 'after_setup_theme', 'my_child_theme_setup' );
function my_child_theme_setup() {
  register_default_headers( array(
    'pgsm' => array(
    'url' => trailingslashit( get_stylesheet_directory_uri() ).'/images/headers/logo.png',
    'thumbnail_url' => trailingslashit( get_stylesheet_directory_uri() ).'/images/headers/logo-thumbnail.png',
    /* translators: header image description */
    'description' => __( 'PGSM', 'pgsm-boilerplate-child' )
    )
  ) );
}
?>