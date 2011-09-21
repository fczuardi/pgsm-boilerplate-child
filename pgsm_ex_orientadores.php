<?php
/*
Template Name: Listagem de Ex-Orientadores
*/
$page_title = __('Orientadores', 'pgsm-boilerplate-child');
$page_subtitle = __('Ex-Orientadores', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Ex-Orientadores');
$list_item_type = 'pgsm_orientador';
$post_subtype = 'ex_orientador';
$post_per_page = 10;
get_template_part( 'pgsm_people_list' ); 
?>