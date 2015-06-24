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

	$link_produto = "http://www.camisariacolombo.com.br/camisa-social-infantil-azul-listrada-40505802000017t/p";

	$html = file_get_html($link_produto);
	
	$categoria = array();
	$breadCrumb = $html->find('div.bread-crumb',0);

	
	
//echo $breadCrumb->find('a')->plaintext;die();
//echo '<pre>';print_r($breadCrumb)->children(1)->children(1);die();
//echo '<pre>';var_dump($breadCrumb->find('ul',0));die();
	//if(count($breadCrumb) > 0){

		foreach ($breadCrumb->find('a') as $value) {
			

			if( strcmp($value->plaintext, "Camisaria Colombo") != 0)
			$categoria[] = $value->plaintext;		
		}
		$categoria = implode("|", $categoria);
	//}		

	echo $categoria;