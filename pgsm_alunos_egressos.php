<?php
/*
Template Name: Listagem de Alunos de Doutorado
*/
$page_title = __('Alunos Egressos', 'pgsm-boilerplate-child');
$page_subtitle = __('Lista Completa', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Alunos');
$list_item_type = 'pgsm_aluno';
$post_subtype = 'egresso';
$post_per_page = 3;
get_header(); 
get_template_part( 'pgsm_people_list' ); 
get_footer(); 
?>