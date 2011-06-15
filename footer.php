		</section><!-- #main -->
		<footer role="contentinfo" class="vcard">
			<a href="<?php echo home_url( '/' ) ?>" 
			  title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" 
			  rel="home" id="logo-footer" class="url fn org"><img src="<?php bloginfo('stylesheet_directory');?>/images/logo-footer.png" /></a>
			  
      <h2 class="fn org">Pós-graduação em Saúde Mental &nbsp;:&nbsp;
        Depto. de Neurociências e Ciências do Comportamento da Faculdade de Medicina de Ribeirão Preto &nbsp;:&nbsp; USP</h2>
      <p class="adr">
        <span class="street-address">Av. Tenente Catão Roxo, 2650</span> &nbsp;:&nbsp;
        <span class="locality">Ribeirão Preto</span> - <span class="region">SP</span> - &nbsp;<span class="country-name">Brasil</span> &nbsp;:&nbsp;
        CEP <span class="postal-code">14051-140</span><br>
        Tel: <a class="tel" href="tel:+55-16-36024607">+55 (16) 3602.4607</a> &nbsp;:&nbsp;
        Fax: <a class="tel" href="tel:+55-16-36024605">+55 (16) 3602.4605</a> &nbsp;:&nbsp;
        <a class="email" href="mailto:pg_saudemental@rnp.fmrp.usp.br">pg_saudemental@rnp.fmrp.usp.br</a>
      </p>
		</footer><!-- footer -->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	wp_footer();
?>
	</body>
</html>