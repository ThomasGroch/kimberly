<?php
	require __DIR__ . '/importador.php';
	
	class Afilio extends Importador{

		var $produto = array();

		var $link_produto = '';
		
		### METODOS DE AFILIADO ###

		public function getLastPage(){
			//return ceil($this->xml_em_array['total']/50); <-- programar
		}

		public function getProductsList(){

			if(isset($this->xml_em_array)){
				if(array_key_exists ('produto' ,$this->xml_em_array)){

					if ( count($this->xml_em_array['produto']) > 0 && ! empty($this->xml_em_array['produto'] )  ) {
						return $this->xml_em_array;

					}else{
						$this->logger->info('[Skip] Lista de produtos vazia > '. json_encode($this->xml_em_array) );
						return false;
					}
				
				}else{
					$this->logger->info('[Skip] Foi informado um XML vazio > '. json_encode($this->xml_em_array) );
					return false;
				}

			}else{
				$this->logger->info('[Skip] Não foi recebido o XML > '. json_encode($this->xml_em_array) );
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