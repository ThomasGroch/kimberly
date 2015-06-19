<?php

	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	date_default_timezone_set("America/Sao_Paulo");
	//include_once 'bootstrap.php';
	//$url = true;
	//$xmlZanox = new Zanox($xml, $url);
	//new SimpleXMLElement($xml, LIBXML_NOEMPTYTAG, true);
	
	
	// Carrega classes do composer e funcoes uteis =)
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/util.php';
	require __DIR__ . '/simple_html_dom.php';
	
	/*
	* Carrega o logger
	* https://github.com/wasinger/simplelogger
	* Ex: $logger->info('Mensagem Exemplo');
	*/
	$logger = new \Wa72\SimpleLogger\FileLogger('./logs/logfile.txt');
	
	/*
	* Carrega padrao que será usado
	*/
	$conf_padrao = 'Zanox';
	require __DIR__ . '/padroes/'.$conf_padrao.'.php';

	$url = "http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=13521&items=500&page=";
	$page = 0;
	$last_page = 1;


	// Inicia loop
	while ($page <= $last_page) {

		// obtem xml da pagina
		$xml = simplexml_load_string(get_content($url.$page) );

		// Converte para array
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);

		// Instancia um novo produto
		$padrao = new $conf_padrao($array);

		// Seta ultima pagina
		$last_page = $padrao->getLastPage();

		// Inicia Loop entre os produtos
		foreach ($array['productItems']['productItem'] as $key => $produto) {
			$logger->info('-');
			
			// Validação
			if( ! $padrao->validate($produto) ){
				continue;
			}
			//exit;

			// Tratamento
			if( ! $padrao->prepare($produto) ){
				continue;
			}

			// Salva produto no xml
			//$padrao->produto;
		}

		// Próxima página
		$page++;
	}
	



