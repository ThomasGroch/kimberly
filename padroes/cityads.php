<?php
	require __DIR__ . '/importador.php';

	Class Cityads extends Importador {

		var $produto = array();

		public function Cityads(){
			parent::__construct();
		}

		public function getLastPage(){
			if ( $this->single_page == TRUE ){ return 999; }
			return ceil($this->xml_em_array['data']['total'] / 1000);
		}

		public function setProduct($produto){
			$produto['url'] = 'http:'.$produto['url'];
			$this->produto = $produto;
		}

		public function getProductsList(){
			if( !isset($this->xml_em_array['data']['items']['item']) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos nao esta no XML > '. json_encode($this->xml_em_array) );
				return false;
			}
			$product_list = $this->xml_em_array['data']['items']['item'];
			if ( empty($product_list) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos esta vazia > '. json_encode($this->xml_em_array) );
				return false;
			}
			return $product_list;
		}

		public function getProductUrl() {
			$url = $this->produto['url'];
			$html = str_get_html(get_content($url));
			if( ! $html ){
				return false
			}

			$a = $html->find('a',0);
			$link_verdadeiro = $a->href;
			if( ! $link_verdadeiro ) {
				$this->logger->info('['.PADRAO.'][DEBUG] Nao achei o link verdadeiro: '.$url);
			}
			$url2 = 'http://cityadspix.com'.$link_verdadeiro;
			
			$html2 = str_get_html(get_content($url2));

			$url3 = $html2->find('a',0)->href;

			return $url3;
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		// Nessa loja todos dados extras sempre estao disponiveis
		// entao, caso nao encontre algum dado do produto ele sera descartado
		public function prepare(){
			return parent::prepare();
		}

	}