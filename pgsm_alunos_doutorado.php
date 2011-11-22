<?php
/*
Template Name: Listagem de Alunos de Doutorado
*/
$page_title = __('Alunos', 'pgsm-boilerplate-child');
$page_subtitle = __('Doutorado', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Alunos');
$list_item_type = 'pgsm_aluno';
$post_subtype = 'doutorando';
$post_per_page = -1;
get_template_part( 'pgsm_people_list' ); 
?>