<?php

	ini_set('upload_max_filesize','3000M');
	ini_set('max_execution_time', 0);
	ini_set('memory_limit','4000M');
	ini_set('post_max_size','3000M');

	set_time_limit(0);

	ini_set('display_errors',1);
	ini_set('display_startup_erros',1);
	error_reporting(E_ALL);
	setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
	date_default_timezone_set('America/Sao_Paulo');	

	include_once("simple_html_dom.php");
	include_once("util.php");




	//$link_produto = "http://www.ray-ban.com/brazil/graduados/gradplp/rb5245_167065?par=zanox&skuId=805289517535";
	$link_produto = "http://www.marcyn.com.br/biquini-basico-band/p?idsku=511564&utm_source=Afilio&utm_campaign=xml1&utm_medium=cpa";

	$html = file_get_html($link_produto);


	$script = $html->find('head',0)->lastChild();
	//echo $script;
	//highlight_string($script);
	$json = get_string_between($script, "skuJson_0 = ", ";");

	//echo $json;
	$arr = json_decode($json,true);

	//echo '<pre>';print_r($arr);
//die();

$i = 0;	
$tamanhos = array();
$cores = array();
$cor = "";
$tamanho = "";
if(count($arr) > 0){
		foreach ($arr['skus'] as $key) {
			
			if($key['available']){
				$tamanho = $key['dimensions']['Tam'];
				$cor = $key['dimensions']['Cor'];
				$cores[] = $cor;
				$tamanhos[] = $tamanho;
			}
		}
		$tamanhos = array_unique($tamanhos);
		$tamanhos = implode("|", $tamanhos);

		$cores = array_unique($cores);
		$cores = implode("|", $cores);
	}
	
	echo "Tamanho: ". $tamanhos."<br>";
	echo "cor: ".$cores;








	
/*
	$script = $html->find('script[xml:space=preserve]',0);
//	highlight_string( $script );

	
	
	$jsonCor = get_string_between($script, "skusDados = ", ";");
	//$jsonCor = rtrim(trim($jsonCor),";");
	//echo $jsonCor;
	//$jsonCor = json_encode($jsonCor);
	$jsonCor =  '{
  "skus":[
    {
      "id": "8053672080896",
      "COR": "Azul",
      "POSITION_COR": 6,
      "Cor Front": "Azul ",
      "POSITION_COR FRONT": 69,
      "Filtro cor front": "Azul ",
      "POSITION_FILTRO COR FRONT": 70,
      "Cor interna front": "Azul ",
      "POSITION_COR INTERNA FRONT": 40,
      "Filtro cor interna front": "Marrom",
      "POSITION_FILTRO COR INTERNA FRONT": 5,
      "Cor externa da aste": "Azul ",
      "POSITION_COR EXTERNA DA ASTE": 132,
      "Filtro Cor externa da aste": "Azul ",
      "POSITION_FILTRO COR EXTERNA DA ASTE": 24,
      "Cor interna da aste": "Azul ",
      "POSITION_COR INTERNA DA ASTE": 135,
      "Filtro cor interna da aste": "Marrom",
      "POSITION_FILTRO COR INTERNA DA ASTE": 1,
      "Cor da lente": "Lentes de Demonstração ",
      "POSITION_COR DA LENTE": 160,
      "Filtro cor da lente": "Lentes de Demonstração ",
      "POSITION_FILTRO COR DA LENTE": 85,
      "Tamanho Lens-bridge": "54-17",
      "POSITION_TAMANHO LENS-BRIDGE": 99,
      "Comprimento da aste": "14,5cm",
      "POSITION_COMPRIMENTO DA ASTE": 11,
      "SKU Ray Ban": "RB5245521954",
      "POSITION_SKU RAY BAN": 1694,
      "GRID": 521954,
      "POSITION_GRID": 1297,
      "MODEL_SIZE": "RB5245",
      "POSITION_MODEL_SIZE": 196,
      "MODEL_COLOR": "Azul com Havana",
      "POSITION_MODEL_COLOR": 59,
      "MODEL_SIZE_DISPLAY": 54,
      "POSITION_MODEL_SIZE_DISPLAY": 10,
      "NEWCOLORS": "Newcolors",
      "POSITION_NEWCOLORS": 0,
      "NEWMODELS": "Newmodels",
      "POSITION_NEWMODELS": 0,
      "COR": "Azul ",
      "POSITION_COR": 29,
      "TAMANHO": 54,
      "POSITION_TAMANHO": 7,
      "SKU RB+Lens Bridge": "RB5245521954 54-17",
      "POSITION_SKU RB+LENS BRIDGE": 1072,
      "COLOR_CODE": 5219,
      "POSITION_COLOR_CODE": 0,
      "garantia": "false"
    }]}';

	$arrCor = json_decode($jsonCor, true);

	//echo json_last_error_msg(); die();

	var_dump($arrCor);
	//echo '<pre>';print_r($arrCor);

	echo "<br><br><br><br><br><br>";

	$jsonAval = get_string_between($script, "skusAvailable = ", ";");
	//$jsonCor = rtrim(trim($jsonCor),";");
	//echo $jsonAval;
	
	$arrAval =  json_decode($jsonAval, true);	

	echo '<pre>';print_r($arrAval);

*/

/*
	$inicio = strpos($script,"skusDados");
	
	$cor = substr(trim($script), $inicio);
	
	$fim = strpos($cor,"var");

	$cor = substr(trim($script), $inicio, $fim);
	
	$cor = trim($cor);
	


	$cor = rtrim($cor,";");
	
	
	echo $cor;*/