<?php
	ini_set('upload_max_filesize','3000M');
	ini_set('max_execution_time', 0);
	ini_set('memory_limit', -1);
	ini_set('post_max_size','3000M');

	set_time_limit(0);

	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');	
	
	// Remove warnings
	error_reporting(E_ERROR | E_PARSE);

	// Carrega classes do composer e funcoes uteis =)
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/util.php';
	require __DIR__ . '/simple_html_dom.php';
	require __DIR__ . '/array2xml.php';
	require __DIR__ . '/contador.php';

	
	/*
	* Carrega padrao que será usado
	* Se for passado pelo primeiro parametro no terminal usa argv[1]
	* ou passado pelo GET[padrao] usa GET[padrao]
	* Ex.: amaro olook
	*/

	$conf_padrao = ( isset($argv[1]) ) ? $argv[1] : null;
	$conf_padrao = ( isset($_GET['padrao']) ) ? $_GET['padrao'] : $conf_padrao;
	if(empty($conf_padrao)){
		echo 'Informe o padrao desejado';
		$logger->info('Informe o padrao desejado');
	}
	define('PADRAO', $conf_padrao);
	define('PADRAO_UC', ucfirst($conf_padrao));

	/*
	* Carrega o logger
	* https://github.com/wasinger/simplelogger
	* Ex: $logger->info('Mensagem Exemplo');
	*/
	$logger = new \Wa72\SimpleLogger\FileLogger('./logs/log-'.PADRAO.'.txt');

	require __DIR__ . '/padroes/'.PADRAO.'.php';

	$page = 0;
	$last_page = 1;

	// Array de produtos processados
    $processed_products = array();

	while ($page <= $last_page ) {

		if( PADRAO == 'zattini'){
			if($page < 10) { $page = 210;}
			if($page > 211) {break;}
		}
		######################
		## Loop de Paginas  ##
		######################

		// Instancia um novo produto
		$temp_var = PADRAO_UC;
		$importador = new $temp_var();

		$importador->load_xml_page($page);

		// Seta ultima pagina
		$last_page = $importador->getLastPage();

		// Obtem a lista de produtos do XML conforme o padrao
		$products_list = $importador->getProductsList();

		// Obtem a lista de produtos para comecar o loop
		// Esta funcao tambem serve para identificar quando chegou a ultima
		// ultima pagina
		if( ! $products_list ) {
			$logger->info('Varredura terminada!');
			break;
		}

		foreach ($products_list as $key => $produto) {
			if( PADRAO == 'zattini'){
				if($key > 15) {break;}
			}
			######################
			## Loop de Produtos ##
			######################
			$logger->info('['.PADRAO.'][Pag '.$page.'/'.$last_page.'][Produto '.$key.']');
			// Informa o produto no qual sera processado
			$importador->setProduct($produto);

			// Validação
			if( ! $importador->validate() ){
				Contador::add('Invalido');
				continue;
			}

			// Tratamento
			if( ! $importador->prepare() ){
				Contador::add('Incompleto');
				continue;
			}

			// Remove a chave @attributes
			recursive_unset($importador->produto, '@attributes');

			// Adiciona o produto processado a lista de produtos prontos
			$processed_products[] = $importador->produto;
			Contador::add('Completo');

			flush();
			 /*if($key >= 5){
			 	break;
			 }*/
		}

		//Flag para rodar 2 paginas
		/* if ( $page >= 1 ){
		 	$logger->info('Script interrompido pela Flag');
		 	break;
		 }*/

		// Próxima página
		$page++;
	}

// Mostra resultado da importacao
$logger->info('['.PADRAO.']Resultado: '.Contador::write('Completo').Contador::write('Invalido').Contador::write('Incompleto') );

$importador->save_xml_products( $processed_products );



