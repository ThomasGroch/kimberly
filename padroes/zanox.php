<?php

	Class Zanox {

		/* Campos Extra */
		public $array = array();
		public $produto = array();
		public $link_do_produto;
		//código de afiliado do tagbox.
		public $affiliate_id = '1916212';
		public $site_name = 'Tag Box';

		public $white_list_categories = array();
		public $black_list_categories = array();

		public function Zanox($array = array()){
			/*
			* Traz instancia do logger para uso na classe
			*/
			global $logger;
			$this->logger = $logger;
			if( !empty($array) ){
				$this->array = $array;
			}
		}

		public function init($array){
			$this->array = $array;
		}

		public function getLastPage(){
			return ceil($this->array['total']/50);
		}

		public function setProduct($produto){
			$this->produto = $produto;
			// Seta categoria
			$this->setCategory();
		}

		public function getProductsList(){
			if( !isset($this->array['productItems']) OR !isset($this->array['productItems']['productItem']) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos nao esta no XML > '. json_encode($this->array) );
				return false;
			}
			$product_list = $this->array['productItems']['productItem'];
			if ( empty($product_list) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos esta vazia > '. json_encode($this->array) );
				return false;
			}
			return $product_list;
		}


		/**
		 * @param array de um produto
		 *  Forma generica de definir a lista de categorias dos XMLs
		 * @return bool
		*/
		public function setCategory(){
			if( isset($this->produto['merchantCategory']) ){
				$categorias = $this->produto['merchantCategory'];
				// Se for dividido por barra, troca para |
				if( strpos($categorias, ' / ') ){
					$categorias = explode(' / ', $this->produto['merchantCategory']);
					$categorias = implode('|', $categorias);
				}
				$this->produto['categorias'] = $categorias;
			}
		}

		/* Forma generica de obter a lista de categorias dos XMLs */
		public function getCategory(){
			return $this->produto['categorias'];
		}

		public function getProductUrl(){
			return $this->produto['trackingLinks']['trackingLink']['ppc'];
		}

		/*
		* Se for um produto que interessar a tagbox, 
		* devera retornar true
		* Entrada: array de um produto
		* Saida: (bool) 
		*/
		public function validate(){

			$categoria_principal = explode('|', $this->getCategory() );
			$categoria_principal = $categoria_principal[0];

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
			
			// Testa resposta do cabeçalho HTTP
			// retorna falso se o link nao estiver funcionando
			// retorna o link se estiver funcionando
			$this->link_do_produto = testHeader( $this->getProductUrl() );
			if ( ! $this->link_do_produto ) {
				$this->logger->info('['.PADRAO.'][Skip] Link quebrado');
				return false;
			}

			return true;
		}

		/**
		 *@param  array() de um produto
		 * Funcao para capturar dados extras
		 * @return array() de um produto + campos extras
		*/
		public function prepare(){
			// Obtem html
			$html = str_get_html(get_content($this->link_do_produto));

			if( ! $html ){
				$this->logger->info('['.PADRAO.'][Skip] HTML invalido');
				return false;				
			}

			// Html retornou vazio?
			if( $html->plaintext == '' ) {
				$this->logger->info('['.PADRAO.'][Skip] Html vazio > '.$this->link_do_produto);
				return false;
			}

			// Obtem loja
			$this->produto['loja'] = get_class($this);

			return $html;
		}
	}