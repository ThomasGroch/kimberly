<?php
	require __DIR__ . '/zanox.php';

	Class Capitollium extends Zanox{

		public $black_list_categories = array();

		public function Capitollium($array = array()){
			parent::__construct($array);
			$this->loja = 'capitollium';
		}

		public function getLastPage(){
			// executa somente 1 pagina
			return -1;
		}

		public function setProduct($produto){
			$produto['url'] = 'http:'.$produto['url'];
			$this->produto = $produto;
		}

		public function setCategory(){
			$this->produto['categorias'] = $this->produto['category'];
		}

		public function getProductsList(){
			if( !isset($this->array['data']['items']['item']) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos nao esta no XML > '. json_encode($this->array) );
				return false;
			}
			$product_list = $this->array['data']['items']['item'];
			if ( empty($product_list) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos esta vazia > '. json_encode($this->array) );
				return false;
			}
			return $product_list;
		}

		/*
		* Se for um produto que interessar a tagbox, 
		* devera retornar true
		* Entrada: array de um produto
		* Saida: (bool) 
		*/
		// public function validate(){
			//parent::validate();
		// }

		/*
		* Funcao para retornar url do sistema de afiliados
		* sem o numero de paginacao
		*/
		public function getXmlUrl() {
			return 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM4MDM1';
		}

		public function getProductUrl() {
			$url = $this->produto['url'];
			$html = file_get_html($url);
			$url2 = 'http://cityadspix.com'.$html->find('a',0)->href;
			$html2 = file_get_html($url2);
			$url3 = $html2->find('a',0)->href;
			return $url3;
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			$html = parent::prepare();
			echo $html;
			exit;
			// Obtem tamanho
			// <span class="attr-name unavailable">
			$tamanhos = '';
			$tamanho_spans = $html->find('span.attr-name');
			if( ! $tamanho_spans ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi foi possivel encontrar tamanhos');
			}

			foreach( $tamanho_spans as $span){
				if( strpos($span->class, 'unavailable') ){
					continue;
				}
				$tamanhos .= trim($span->plaintext).'|';
			}
			$tamanhos = substr($tamanhos, 0, -1);
			$this->produto['tamanhos'] = $tamanhos;


			// Obtem cor
			// <a href="?color=138" class="attr-name unavailable"
			$cores = '';
			$cor_as = $html->find('a.attr-name');
			if( ! $cor_as ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi foi possivel encontrar cores');
			}

			foreach( $cor_as as $a){
				if( strpos($a->class, 'unavailable') ){
					continue;
				}
				$cores .= trim($a->plaintext).'|';
			}
			$cores = substr($cores, 0, -1);
			$this->produto['cores'] = $cores;

			// Obtem marca
			$this->produto['marca'] = 'Capitollium';

			// Produto Tratado com sucesso
			return true;
		}
	}