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
	require __DIR__ . '/xmlconstruct.php';
	
	/*
	* Carrega o logger
	* https://github.com/wasinger/simplelogger
	* Ex: $logger->info('Mensagem Exemplo');
	*/
	$logger = new \Wa72\SimpleLogger\FileLogger('./logs/logfile.txt');
	
	/*
	* Carrega padrao que será usado
	*/
	$conf_padrao = 'Amaro';
	require __DIR__ . '/padroes/'.$conf_padrao.'.php';

	$url = "http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=13521&items=500&page=";
	$page = 0;
	$last_page = 1;


	// XML que ira salvar os produtos processados
    $XmlConstruct = new XmlConstruct('root', '', 'xmls/'.$conf_padrao.'.xml');
    $products_finish = array();

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

		// Obtem a lista de produtos do XML conforme o padrao
		$products_list = $padrao->getProductsList();

		// Inicia Loop entre os produtos
		foreach ($products_list as $key => $produto) {
			$logger->info('[Pag '.$page.'/'.$last_page.'][Produto '.$key.']');

			// Informa o produto no qual sera processado
			$padrao->produto = $produto;

			// Validação
			if( ! $padrao->validate() ){
				continue;
			}

			// Tratamento
			if( ! $padrao->prepare() ){
				continue;
			}

			// Remove a chave @attributes
			recursive_unset($padrao->produto, '@attributes');

			// Adiciona o produto processado a lista de produtos prontos
			$products_finish['products'][]['product'] = $padrao->produto;

		}

		// Flag para rodar 2 paginas
		// if ( $page >= 0 ){
		// 	$logger->info('Script interrompido pela Flag');
		// 	break;
		// }

		// Próxima página
		$page++;
	}
//print_r($products_finish);
// Salva produto no xml

$XmlConstruct->fromArray($products_finish);
$XmlConstruct->getDocument();

//$xml = Array2XML::createXML('root_'.$conf_padrao, $products_finish);
//$xml->saveXML('./xmls/'.$conf_padrao.'.xml');

$logger->info('Produtos salvos!');
	



