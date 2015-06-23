<?php
	include_once("simple_html_dom.php");

	$link_produto = "http://www.hopelingerie.com.br/calcinha-biquini-nude-49.aspx/p";

	$html = file_get_html($link_produto);
	$label = $html->find('body',0)->find('script',37);

	//remove a marcação de script.
	$script = str_replace('<script language="javascript">', '', trim($label));
	$script = str_replace('window.chaordic_meta =', '', trim($script));
	$script = str_replace('</script>', '',$script);
	
	
	/*	erros json php
		
		0 = JSON_ERROR_NONE
		1 = JSON_ERROR_DEPTH
		2 = JSON_ERROR_STATE_MISMATCH
		3 = JSON_ERROR_CTRL_CHAR
		4 = JSON_ERROR_SYNTAX
		5 = JSON_ERROR_UTF8
	*/

	//remove bloco com função que ocasiona o erro JSON_ERROR_SYNTAX
	$script = substr($script,58);
	$script = ltrim($script," ,");
	$script = "{". $script;
	
	$arr = json_decode($script, true);

	$cor = array();
	$tamanho = array();
	$strCor = "";
	
	foreach ($arr['product']['skus'] as $key => $value) {
		
		if($value['status'] == 'available'){
			$strCor = rtrim($value['specs']['cor'],1);
			$tamanho[] = $value['specs']['tamanho'];
			$cor[]= $strCor;
		}	
	}

	echo '<pre>';print_r(array_unique($cor));
	echo '<pre>';print_r(array_unique($tamanho));

