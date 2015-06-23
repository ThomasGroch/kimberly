<?php

	Class Zanox {

		/* Campos Extra */
		public $array = array();
		public $produto = array();
		public $link_do_produto;

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

		public function getProductsList(){
			$product_list = $this->array['productItems']['productItem'];
			if ( empty($product_list) ) {
				$this->logger->info('[Skip] Impossivel de obter a lista de produtos desta pagina');
				exit;
			}
			return $product_list;
		}

		/*
		* Se for um produto que interessar a tagbox, 
		* devera retornar true
		* Entrada: array de um produto
		* Saida: (bool) 
		*/
		public function validate(){
			/* 
			* $categorias[0] é a Categoria principal
			* $categorias[1] e [2] são sub categorias
			*/
			if( isset($this->produto['merchantCategory']) ){
				$categorias = explode(' / ', $this->produto['merchantCategory']);
				
				// Se a categoria principal NÃO estiver na lista de categorias válidas
				// retorna falso
				if( ! empty($this->categorias_validas) AND ! in_array($categorias[0], $this->categorias_validas) ){
					$this->logger->info('[Skip] Categoria invalida > '.$categorias[0]);
					return false;
				}
			}
			
			// Testa resposta do cabeçalho HTTP
			// retorna falso se o link nao estiver funcionando
			// retorna o link se estiver funcionando

			$this->link_do_produto = testHeader($this->produto['trackingLinks']['trackingLink']['ppc']);
			if ( ! $this->link_do_produto ) {
				$this->logger->info('[Skip] Link quebrado');
				return false;
			}

			return true;
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			// Obtem html
			$html = str_get_html(get_content($this->link_do_produto));

			if( ! $html ){
				$this->logger->info('[Skip] HTML invalido');
				return false;				
			}

			// Html retornou vazio?
			if( $html->plaintext == '' ) {
				$this->logger->info('[Skip] Html vazio > '.$this->link_do_produto);
				return false;
			}

			// Obtem loja
			$this->produto['loja'] = get_class($this);

			return $html;
		}
	}