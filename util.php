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
function testHeader($url='') {
	global $logger;
	
  // Testa se eh um link
  // if(! filter_var($url, FILTER_VALIDATE_URL) === FALSE){
  //   // Se for nao for link entao printa ele
  //   $logger->info('Link estranho > '.$url);
  // }

	$headers = get_headers($url, 1);

    $link_do_produto = getRedirectUrl($url);
    if( !  $link_do_produto ) {
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

    // BUG do retorno em array após o redirecionamento
    if( is_array($url)) {
        $url = $url[0];
    }

    $headers = get_headers($url, 1);
    if ($headers !== false && isset($headers['Location'])) {
        return getRedirectUrl($headers['Location']);
    }

    // Aqui a variavel $headers corresponde a pagina
    // do produto da loja, entao testo o retorno http
    if( strpos( $headers[0], 'OK' ) ){
        return $url;
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

function get_string_between($string, $start, $end=''){
    $string = " ".$string;
    $ini = strpos($string,$start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    if (empty($end)){
        return substr($string,$ini);
    }
    $len = strpos($string,$end,$ini) - $ini;
    return substr($string,$ini,$len);
}
?>