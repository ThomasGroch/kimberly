<?php

class Importador {

	var $xml_em_array = array();

	var $produto_array = array();

	// Esta variavel terá o valor retornado pela funcao getProductUrl() da loja
	var $link_do_produto = '';

	var $single_page = FALSE;

   function __construct() {
		/*
		* Traz instancia do logger para uso na classe
		*/
		global $logger;
		$this->logger = $logger;
	}

	/*
	* Funcao para retornar url do sistema de afiliados
	* caso a pagina seja -1 retorna sem a paginacao
	*/
	public function getXmlUrl( $page = 0 ) {
		return ( $page < 0 OR $this->single_page ) ? $this->xml_url : $this->xml_url . $page;
	}

	/*
	* Carrega a pagina do xml
	* Converte em array
	* Coloca array no $this->xml_em_array
	*/
	public function load_xml_page($page) {
		// obtem xml da pagina
		$xml = simplexml_load_string(get_content( $this->getXmlUrl($page) ));

		// Converte para array
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);

		// Coloca o xml convertido para array o obj padrao
		$this->xml_em_array = $array;

	}

	public function save_xml_products( $processed_products ) {
		// Salva produto no xml
		$file_path = 'xmls/'.PADRAO.'-'.date('Y-m-d-H-i-s').'.xml';

		if(empty($processed_products)){
			$this->logger->info('['.PADRAO.']Nenhum produo encontrado. Sem conexao a internet?');
			exit;
		}

		try 
		{
		    $xml = new array2xml('products', 'product');
		    $xml->createNode( $processed_products );
		    $xml->save( $file_path );
			$this->logger->info('['.PADRAO.']Produtos salvos!');
		} 
		catch (Exception $e) 
		{
		    echo $e->getMessage();
			$this->logger->info('['.PADRAO.']Save XML catch '.$e->getMessage());
		}

	}


	public function filter_category_lists() {

		$categoria_principal = $this->getCategory();
		if( empty($categoria_principal) ) {
			$this->setCategory();
			$categoria_principal = $this->getCategory();
		}

		// White List filter
		// Se a categoria principal NÃO estiver na lista branca de categorias
		// retorna falso
		if( ! in_array($categoria_principal, $this->white_list_categories ) AND
			! empty($this->white_list_categories) ){
		
			$this->logger->info('['.PADRAO.'][Skip] Category WhiteList Filter > '.$categoria_principal);
			return false;

		}

		// Black List filter
		// Se a categoria principal ESTIVER estiver na lista negra de categorias
		// retorna falso
		if( in_array($categoria_principal, $this->black_list_categories ) AND
			! empty($this->black_list_categories) ){
		
			$this->logger->info('['.PADRAO.'][Skip] Category BlackList Filter > '.$categoria_principal);
			return false;
		
		}

		return true;
	}

	/*
	* Se for um produto que interessar a tagbox, 
	* devera retornar true
	* Entrada: array de um produto
	* Saida: (bool) 
	*/
	public function validate(){

		// Testa resposta do cabeçalho HTTP
		// retorna falso se o link nao estiver funcionando
		// retorna o link se estiver funcionando
		$url_verdadeira = $this->getProductUrl();
		if( ! $url_verdadeira ) {
			// Nao foi possivel obter link verdadeiro
			return false;
		}
		$this->link_do_produto = testHeader( $url_verdadeira );
		if ( ! $this->link_do_produto ) {
			$this->logger->info('['.PADRAO.'][Skip] Link quebrado');
			return false;
		}

		return true;
	}

	/**
	 * Funcao para capturar dados extras
	 * @return array() de um produto + campos extras
	*/
	public function prepare(){
		// Obtem html
		$this->html = str_get_html( get_content($this->link_do_produto) );

		if( ! $this->html ){
			$this->logger->info('['.PADRAO.'][Skip] HTML invalido > '.$this->link_do_produto);
			return false;				
		}

		// Html retornou vazio?
		if( $this->html->plaintext == '' ) {
			$this->logger->info('['.PADRAO.'][Skip] Html vazio > '.$this->link_do_produto);
			return false;
		}

		// Filtra White/Black List
		if( ! $this->filter_category_lists() ) {
			return false;
		}

		// Obtem loja
		if(empty($this->produto['loja']))
			$this->produto['loja'] = get_class($this);

	}

}