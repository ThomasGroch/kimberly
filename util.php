<?php
function get_content($url) {
	// create a new cURL resource
	$ch = curl_init();

	// set URL and other appropriate options
	// Mais opcoes em:
	// http://php.net/manual/en/function.curl-setopt.php
	$options = array(CURLOPT_URL => $url,
					 CURLOPT_RETURNTRANSFER => true,
	                 CURLOPT_HEADER => false
	                 //,CURLOPT_FOLLOWLOCATION => true
	                );

	curl_setopt_array($ch, $options);

	// grab URL and pass it to the browser
	$res = curl_exec($ch);

	// close cURL resource, and free up system resources
	curl_close($ch);
	return $res;
}

/*
* Retorna False se a pagina nao for encontrada
* caso contrario retorna url do produto
*/
function testHeader($url) {
	global $logger;
	
	$headers = get_headers($url, 1);
	if (strpos($headers[0], '404') ){
		$logger->info('Link > '.$url.' >'. $headers[0]);
		return false;
	}
	$link_do_produto = $headers['Location']['0'];
	//$logger->info('locato:'.$headers['Location']['0']);
	return $link_do_produto;
}
?>