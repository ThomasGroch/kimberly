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
	* Parametros de configuracao do Indexador Kimberly
	* 1. nomedaloja
	* 2. start_page 	( numero da pagina que ira iniciar )
	* 3. start_step		( numero do produto no qual ira comecar )
	* 4. end_page 		( numero da pagina que ira iniciar )
	* 5. end_step		( numero do produto no qual ira comecar )
	*
	* Ex.: php index.php amaro 2 3 4 5
	* Nesse exemplo ira indexar da pagina 2 a partir do terceiro produto 
	* e ira parar na pagina 4 no quinto produto
	*/

	$conf_padrao = ( isset($argv[1]) ) ? $argv[1] : null;
	$conf_padrao = ( isset($_GET['padrao']) ) ? $_GET['padrao'] : $conf_padrao;

	$conf_start_page = a_or_b_is_null( $argv[2], $_GET['start_page'], 0 );
	$conf_start_step = a_or_b_is_null( $argv[3], $_GET['start_step'] );
	$conf_end_page   = a_or_b_is_null( $argv[4], $_GET['end_page'] );
	$conf_end_step   = a_or_b_is_null( $argv[5], $_GET['end_step'] );

	if(empty($conf_padrao)){
		echo 'Informe o padrao desejado';
		exit;
	}
	define('PADRAO', $conf_padrao);
	define('PADRAO_UC', ucfirst($conf_padrao));

	echo '======================================'.PHP_EOL;
	echo 'Inicio: '.$conf_start_step.'/'.$conf_start_page.PHP_EOL;
	echo 'Fim: '.$conf_end_step.'/'.$conf_end_page.PHP_EOL;
	echo '======================================'.PHP_EOL;
	/*
	* Carrega o logger
	* https://github.com/wasinger/simplelogger
	* Ex: $logger->info('Mensagem Exemplo');
	*/
	$logger = new \Wa72\SimpleLogger\FileLogger('./logs/log-'.PADRAO.'.txt');

	require __DIR__ . '/padroes/'.PADRAO.'.php';

	$page = $conf_start_page;
	$last_page = 1;

	// Array de produtos processados
    $processed_products = array();

	while ($page <= $last_page ) {

		######################
		## Loop de Paginas  ##
		######################

		if( $conf_start_page !== NULL AND $page < $conf_start_page ) {
			$page = $conf_start_page;
			$logger->info('['.PADRAO.']Pulando para pagina #'. $page .' pela flag');
		}

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
			
			######################
			## Loop de Produtos ##
			######################

			// Comecar pelo produto especifico
			if( $conf_start_page !== NULL AND $conf_start_step !== NULL AND
				$page == $conf_start_page AND $key < $conf_start_step ){
				$logger->info('['.PADRAO.']Pulando para produto #'. $key .' pela flag');
				continue;
			}
			// Verifica se esta no ultimo produto
			if( $conf_end_page !== NULL AND $conf_end_page == $page AND
				$conf_end_step !== NULL AND
				$key >= $conf_end_step ) {

				$logger->info('Script interrompido pela Flag');
				break;
			}

			$logger->info('['.PADRAO.'][Pag '.$page.'/'.$last_page.'][Produto '.$key.']');
			// Informa o produto no qual sera processado
			$importador->setProduct($produto);
			
			// $cats[] = $importador->getCategory();
			// continue;


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
			
			
		}

		// Verifica se esta na ultima pagina
		if( $conf_end_page !== NULL AND
			$page >= $conf_end_page ) {
			
			$logger->info('Script interrompido pela Flag');
			break;
		}

		// Próxima página
		$page++;
	}
// print_r(array_unique($cats) );
// Mostra resultado da importacao
$logger->info('['.PADRAO.']Resultado: '.Contador::write('Completo').Contador::write('Invalido').Contador::write('Incompleto') );

$importador->save_xml_products( $processed_products );



