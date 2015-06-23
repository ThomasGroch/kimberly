<?php
	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');	
	
	// Carrega classes do composer e funcoes uteis =)
	require __DIR__ . '/vendor/autoload.php';
	require __DIR__ . '/util.php';
	require __DIR__ . '/simple_html_dom.php';
	require __DIR__ . '/array2xml.php';
	
	/*
	* Carrega o logger
	* https://github.com/wasinger/simplelogger
	* Ex: $logger->info('Mensagem Exemplo');
	*/
	$logger = new \Wa72\SimpleLogger\FileLogger('./logs/logfile.txt');
	
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
	$conf_padrao_uc = ucfirst($conf_padrao);
	require __DIR__ . '/padroes/'.$conf_padrao.'.php';

	$page = 0;
	$last_page = 1;

	// Array de produtos processados
    $products_finish = array();

	// Inicia loop
	while ($page <= $last_page) {

		// Instancia um novo produto
		$padrao = new $conf_padrao_uc();

		// obtem xml da pagina
		$xml = simplexml_load_string(get_content($padrao->getUrl().$page) );

		// Converte para array
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);

		// Coloca o xml convertido para array o obj padrao
		$padrao->init($array);

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
			$products_finish[] = $padrao->produto;
			/* if($key >=3){
			 	break;
			 }
			 */
		}

		//Flag para rodar 2 paginas
		/* if ( $page >= 1 ){
		 	$logger->info('Script interrompido pela Flag');
		 	break;
		 }
		*/
		// Próxima página
		$page++;
	}

// Salva produto no xml
$file_path = 'xmls/'.$conf_padrao.'-'.date('Y-m-d-H-i-s').'.xml';

try 
{
    $xml = new array2xml('products', 'product');
    $xml->createNode( $products_finish );
    $xml->save( $file_path );
} 
catch (Exception $e) 
{
    echo $e->getMessage();
}

$logger->info('Produtos salvos!');




