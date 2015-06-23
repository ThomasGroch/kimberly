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

	if( ! getRedirectUrl($url) ) {
		$logger->info('[Skip] Link corrompido');
		return false;
	}
	return $link_do_produto;
}


function getRedirectUrl($url) {
    stream_context_set_default(array(
        'http' => array(
            'method' => 'HEAD'
        )
    ));
    $headers = get_headers($url, 1);
    if ($headers !== false && isset($headers['Location'])) {
        return getRedirectUrl($headers['Location']);
    }

    // Aqui a variavel $headers corresponde a pagina
    // do produto da loja, entao testo o retorno http

    if( strpos( $headers['Status'], 'OK' ) ){
    	return $url;;
    }
    return false;
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