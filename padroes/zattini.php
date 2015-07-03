<?php
	require __DIR__ . '/zanox.php';

	Class Zattini extends Zanox{

		var $xml_url = 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=15900&items=500&page=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_categories = array('Infantil');

		public function Zattini(){
			parent::__construct();
		}

		public function setProduct() {
			parent::setProduct();
			$this->produto['loja'] = 'Zattini';
		}

		public function setCategory(){
			parent::setCategory();
			$categorias = explode('|', $this->produto['categoria']);
			if( !empty($categorias) ){
				foreach($categorias as $key => $cat){
					$categorias[$key] = str_replace(' Brownshoes', '', $cat);
				}
				$this->produto['categoria'] = $categorias;
			}
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			if( ! parent::prepare() ){ return false; }
			
			// Obtem tamanho
			// <span class="attr-name unavailable">
			$tamanhos = '';
			$tamanho_spans = $this->html->find('span.attr-name');
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
			$this->produto['tamanho'] = $tamanhos;


			// Obtem cor
			// <a href="?color=138" class="attr-name unavailable"
			$cores = '';
			$cor_as = $this->html->find('a.attr-name');
			if( ! $cor_as ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi foi possivel encontrar cores');
			}else{
				foreach( $cor_as as $a){
					if( strpos($a->class, 'unavailable') ){
						continue;
					}
					$cores .= trim($a->plaintext).'|';
				}
				$cores = substr($cores, 0, -1);
			}

			$this->produto['core'] = $cores;

			// Obtem marca
			$this->produto['marca'] = 'Zattini';

			// Produto Tratado com sucesso
			return true;
		}
	}