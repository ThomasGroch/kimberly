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

	if ( $headers[1] != "HTTP/1.1 200 OK" || $headers[2] != "HTTP/1.1 200 OK"){
		$logger->info('[Skip] Resposta '.$headers[1].' > '.$url);
		return false;
	}

	if( (empty($headers['Location'])) || (! is_array($headers['Location'])) ) {
		$logger->info('[Skip] Link do Zanox corrompido > '.$headers['Location']);
		return false;
	}
	
	if(! is_array($headers['Location'])){
		$link_do_produto = $headers['Location'];		
	}else{
		$link_do_produto = $headers['Location']['0'];		
	}

	return $link_do_produto;
}
function recursive_unset(&$array, $unwanted_key) {
	if( isset($array[$unwanted_key]) ){
    	unset($array[$unwanted_key]);
	}
    foreach ($array as &$value) {
        if (is_array($value)) {
            recursive_unset($value, $unwanted_key);
        }
    }
}
?>