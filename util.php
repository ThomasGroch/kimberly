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
	
	//$headers = get_headers($url, 1);

	// Se o Link do zanox estiver funcionando
	// e o redirecionamento estiver retornando
	// um cabecalho com respota 200 entao retorna true
	// caso contrario falha
	// if ( isset($headers[1]) && $headers[1] != "HTTP/1.1 200 OK") || 
	// 	isset($headers[2]) && $headers[2] != "HTTP/1.1 200 OK")  {
	// 	$logger->info('[Skip] Resposta '.$headers[1].' > '.$url);
	// 	return false;
	// }

	// if( (empty($headers['Location'])) || (! is_array($headers['Location'])) ) {
	// 	$logger->info('[Skip] Link do Zanox corrompido > '.$headers['Location']);
	// 	return false;
	// }
	
	// if(! is_array($headers['Location'])){
	// 	$link_do_produto = $headers['Location'];		
	// }else{
	// 	$link_do_produto = $headers['Location']['0'];		
	// }

    $link_do_produto = getRedirectUrl($url);
    if( !  $link_do_produto ) {
        return false;
    }

	return $link_do_produto;
}


function getRedirectUrl($url='') {
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

class Util {
   /**
     * Create XML using string or array
     *
     * @param mixed $data input data
     * @param SimpleXMLElement $xml
     * @param string $child name of first level child
     *
     * @return adding Xml formated data into SimpleXmlElement
     */

	static function data2XML(array $data, SimpleXMLElement $xml, $child = "items")
    {

        foreach($data as $key => $val) {
            if(is_array($val)) {

                if(is_numeric($key)) {
                    $node  = $xml->addChild($child);
                    $nodes = $node->getName($child);
                } else {

                    $node  = $xml->addChild($key);
                    $nodes = $node->getName($key);
                }

                $node->addChild($nodes, self::data2Xml($val, $node));
            } else {
                $xml->addChild($key, $val);
            }
        }

    }

}
?>