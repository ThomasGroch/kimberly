<?php
	require __DIR__ . '/loja.php';
	
	class Afilio extends Loja{



		public function getProductsList(){

			if(isset($this->array)){
				if(array_key_exists ('produto' ,$this->array)){

					if ((count($this->array['produto']) > 0) &&(!empty($this->array['produto']))) {
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

		/**
		 * Funcao para retornar url do sistema de afiliados
		 * sem o numero de paginacao
		*/
		public function getUrl() {
			return 'http://v2.afilio.com.br/aff/aff_get_boutique.php?boutiqueid=39281-895842&token=53e355b5e465b0.28149070&progid=1180&format=XML';
			//return "http://v2.afilio.com.br/aff/aff_get_boutique.php?boutiqueid=39281-895737&token=53e355b5e465b0.28149070&progid=1260&format=XML";
		}

	}