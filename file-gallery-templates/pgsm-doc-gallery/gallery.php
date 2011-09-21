<?php
  $known_extensions = array(
    'DOC',
    'ODP',
    'ODS',
    'ODT',
    'PDF',
    'PPT',
    'XLS',
  );
	$starttag = "div";
	$cleartag = "\n<br class='clear' />\n";
	$filename = get_attached_file( $attachment->ID );
  $doc_path_info = pathinfo($filename);
  $extension = strtoupper($doc_path_info['extension']);
  $plugin_relative_path = substr(plugin_basename(__FILE__), strpos(plugin_basename(__FILE__), 'wp-content')+11 );
  $images_path = str_replace('plugins','',WP_PLUGIN_URL) . 
                  str_replace(basename(__FILE__),'images/',$plugin_relative_path);
  if (in_array($extension, $known_extensions)){
    $thumb_link = $images_path . 'icn_' . $extension . '.png';
  } else {
    $thumb_link = $images_path . 'icn_generico.png';
  }
  $has_more_pages = ($file_gallery_query->max_num_pages > $page);
?>
<dl class="gallery-item<?php echo $endcol; ?>">
	<dt class="gallery-icon">
	<?php if( "" != $link ) : ?>
		<a href="<?php echo $link; ?>" <?php if("" != $link_class) : ?> class="<?php echo $link_class; ?>"<?php endif; ?><?php if("" != $rel) : ?> rel="<?php echo $rel; ?>"<?php endif; ?>>
	<?php endif; ?>
			<img src="<?php echo $thumb_link; ?>" width="70" height="52" title="<?php echo $title; ?>" class="attachment-<?php echo $size ?><?php if( "" != $image_class ){ echo " " . $image_class;} ?>" alt="<?php if( $thumb_alt ){ echo $thumb_alt; }else{ echo $title; }?><?php ?>" />
	    <?php 
	    echo '<span class="doc-name">' . $title . '</span>';
	    $size = filesize( $filename );
	    // code borrowed from http://www.php.net/manual/en/function.filesize.php#100097
      $units = array(' B', ' KB', ' MB', ' GB', ' TB');
      for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
?>
	<?php if( "" != $link ) : ?>
		</a>
	<?php endif; ?>
	</dt>
	<dd class="gallery-caption">
	  <?php
  // $custom_fields = array('tipo_publicacao', 'curso', 'ano_de_publicacao', 'autor', 'orientadores', 'coorientadores');
    $tipo_publicacao = get_post_meta($attachment->ID, "_tipo_publicacao", true);
    $curso_publicacao = get_post_meta($attachment->ID, "_curso", true);
    $ano_publicacao = get_post_meta($attachment->ID, "_ano_de_publicacao", true);
    $autor_publicacao = get_post_meta($attachment->ID, "_autor", true);
    $orientadores_publicacao = get_post_meta($attachment->ID, "_orientadores", true);
    $coorientadores_publicacao = get_post_meta($attachment->ID, "_coorientadores", true);
    echo  '<span class="publicacao-meta">' .
          ($tipo_publicacao ? ucfirst($tipo_publicacao) : '') . 
          ($curso_publicacao ? ' de ' . ucfirst($curso_publicacao) : '') .
          ($autor_publicacao ? ' por ' . $autor_publicacao : '') .
          ($orientadores_publicacao ? ' orientada por ' . $orientadores_publicacao : '') .
          ($coorientadores_publicacao ? ' e coorientada por ' . $coorientadores_publicacao : '') .
          '</span>';
    ?>
		<?php echo '<span class="doc-size">' . round($size, 0).$units[$i] . '</span>'; ?>
	</dd>
</dl>
<?php
if ($has_more_pages){?>
  <div class="navigation">
    <div class="line-button">
      <a href="<?php echo $next_page_link ?>">Mais Documentos</a>
    </div>
  </div>
<?php
}
?>