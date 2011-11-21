<?php
/*
Template Name: Listagem de Orientadores Credenciados
*/
$page_title = __('Orientadores', 'pgsm-boilerplate-child');
$page_subtitle = __('Credenciados', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Orientadores');
$list_item_type = 'pgsm_orientador';
$post_subtype = 'credenciado';
$post_per_page = -1;
get_template_part( 'pgsm_people_list' ); 
?>