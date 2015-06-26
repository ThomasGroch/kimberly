<?php
	require __DIR__ . '/loja.php';
	
	class Afilio extends Loja{

		var $xml_url = 'http://v2.afilio.com.br/aff/aff_get_boutique.php?boutiqueid=39281-895842&token=53e355b5e465b0.28149070&progid=1180&format=XML';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();

		### METODOS DE AFILIADO ###

		public function getLastPage(){
			//return ceil($this->array['total']/50); <-- programar
		}

		public function getProductsList(){

			if(isset($this->array)){
				if(array_key_exists ('produto' ,$this->array)){

					if ( count($this->array['produto']) > 0 && ! empty($this->array['produto'] )  ) {
						return $this->array;

					}else{
						$this->logger->info('[Skip] Lista de produtos vazia > '. json_encode($this->array) );
						return false;
					}
				
				}else{
					$this->logger->info('[Skip] Foi informado um XML vazio > '. json_encode($this->array) );
					return false;
				}

			}else{
				$this->logger->info('[Skip] Não foi recebido o XML > '. json_encode($this->array) );
				return false;
			}
		}

		### METODOS GENERICOS DA LOJA ###

		/**
		 *  Forma generica de definir a lista de categorias dos XMLs
		 * @return bool
		*/
		public function setCategory(){
			// if( isset($this->produto['merchantCategory']) ){
			// 	$categorias = $this->produto['merchantCategory'];
			// 	// Se for dividido por barra, troca para |
			// 	if( strpos($categorias, ' / ') ){
			// 		$categorias = explode(' / ', $this->produto['merchantCategory']);
			// 		$categorias = implode('|', $categorias);
			// 	}
			// 	$this->produto['categoria'] = $categorias;
			// }
		}

		/* Forma generica de obter a lista de categorias dos XMLs */
		public function getCategory(){
			// return $this->produto['categoria'];
		}

		/* Forma generica de obter a lista de categorias dos XMLs */
		public function getProductUrl(){
			// return $this->produto['trackingLinks']['trackingLink']['ppc'];
		}

	}