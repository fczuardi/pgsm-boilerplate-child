		</section><!-- #main -->
    <!--[if lt IE 9 ]>
		<div id="ie6-message">
		  <h1>Você sabia que seu Internet Explorer está desatualizado?</h1>
		  <p>Este site não é compatível com o seu navegador, atualize-o <a href="http://www.google.com/chromeframe">instalando o plugin Chrome Frame</a> ou um dos navegadores modernos abaixo.</p>
		  <ul>
		    <li>
		      <a href="http://www.mozilla.com/firefox/"><img src="<?php bloginfo('stylesheet_directory');?>/ie6/browser_firefox.gif" /></a>
		      <p>Firefox</p>
		    </li>
		    <li>
		      <a href="http://www.google.com/chrome"><img src="<?php bloginfo('stylesheet_directory');?>/ie6/browser_chrome.gif" /></a>
		      <p>Chrome</p>
		    </li>
		    <li style="margin-right:0px">
		      <a href="http://www.apple.com/safari/download/"><img src="<?php bloginfo('stylesheet_directory');?>/ie6/browser_safari.gif" /></a>
		      <p>Safari</p>
		    </li>
		  </ul>
		  <p style="clear:left">Ou até mesmo a última versão do <a href="http://www.microsoft.com/windows/Internet-explorer">Internet Explorer</a>.</p>
		</div>
    <![endif]-->
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
    <script type="text/javascript" src="<?php bloginfo('stylesheet_directory');?>/js/main.js"></script>
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
<?php
	/* Always have wp_footer() just before the closing </body>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to reference JavaScript files.
	 */
	wp_footer();
?>
	</body>
</html>