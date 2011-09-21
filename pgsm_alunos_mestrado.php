<?php
/*
Template Name: Listagem de Alunos de Mestrado
*/
$page_title = __('Alunos', 'pgsm-boilerplate-child');
$page_subtitle = __('Mestrado', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Alunos');
$list_item_type = 'pgsm_aluno';
$post_subtype = 'mestrando';
$post_per_page = 10;
get_template_part( 'pgsm_people_list' ); 
?>