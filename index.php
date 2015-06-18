<?php
	
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);


	include_once 'bootstrap.php';
	
	


	$xml = "http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=13521&items=500&page=0";
	$url = true;

	$xmlZanox = new Zanox($xml, $url);

	$page = 0;
	$last_page = ;

	// Inicia loop
	while ($page <= $last_page) {
		// obtem xml da pagina
		$xml = get_content($url.$page);
		
		//new SimpleXMLElement($xml, LIBXML_NOEMPTYTAG, true);
		// Inicia Loop entre os produtos

			/* ## Validação ##
			* Testa o link
			* Remove caso não seja da categoria desejada
			*/

			/* ## Tratamento ##
			* Obtem cor/tamanho
			* Coloca marca/loja
			*/


		// Próxima página
	}
	



