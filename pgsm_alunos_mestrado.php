<?php
/*
Template Name: Listagem de Alunos de Mestrado
*/
$page_title = __('Alunos de Mestrado', 'pgsm-boilerplate-child');
$page_subtitle = __('Lista Completa', 'pgsm-boilerplate-child');
$next_page_label = __('Mais Alunos');
$list_item_type = 'pgsm_aluno';
$post_subtype = 'mestrando';
$post_per_page = 3;
get_template_part( 'pgsm_people_list' ); 
?>