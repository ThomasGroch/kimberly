<?php

	require __DIR__ . '/importador.php';

	Class Zanox extends Importador {

		/* Campos Extra */
		public $array = array();
		public $produto = array();

	   function __construct() {
	       parent::__construct();
		}


		### METODOS DE AFILIADO ###

		public function getLastPage(){
			if ( $this->single_page ){ return 999; }
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


		### METODOS GENERICOS DA LOJA ###

		/**
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
				$this->produto['categoria'] = $categorias;
			}
		}

		/* Forma generica de obter a lista de categorias dos XMLs */
		public function getCategory(){
			return $this->produto['categoria'];
		}

		/* Forma generica de obter a lista de categorias dos XMLs */
		public function getProductUrl(){
			return $this->produto['trackingLinks']['trackingLink']['ppc'];
		}

	}