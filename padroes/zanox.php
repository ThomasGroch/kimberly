<?php

	Class Zanox{

		// XML convertido em array
		private $array;

		private $categorias_validas = array(
								'Moda Feminina',
				);

		/* Campos Extra */
		$this->array = array();

		function Zanox($array){
			/*
			* Traz instancia do logger para uso na classe
			*/
			global $logger;
			$this->logger = $logger;

			$this->array = $array;
		}

		public function getLastPage(){
			return $this->array['total'];
		}

		/*
		* Se for um produto que interessar a tagbox, 
		* devera retornar true
		* Entrada: array de um produto
		* Saida: (bool) 
		*/
		public function validate($produto){
			/* 
			* $categorias[0] é a Categoria principal
			* $categorias[1] e [2] são sub categorias
			*/
			$categorias = explode(' / ', $produto['merchantCategory']);
			
			// Se a categoria principal NÃO estiver na lista de categorias válidas
			// retorna falso
			// if( ! in_array($categorias[0], $this->categorias_validas) ){
			// 	$this->logger->info('categoria invalida');
			// 	return false;
			// }
			
			// Testa resposta do cabeçalho HTTP
			// retorna falso se o link nao estiver funcionando
			// retorna o link se estiver funcionando
			$this->link_do_produto = testHeader($produto['trackingLinks']['trackingLink']['ppc']);
			if ( ! $this->link_do_produto ) {
				$this->logger->info('Link quebrado');
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
			$html = get_content($this->link_do_produto);

			if( empty($html->plaintext) ) {
				return false;
			}

			// Obtem cor/tamanho
			$tamanhos = $html->find('div.size-option--available');
			foreach($tamanhos as $tamanho){
				 $this->array['tamanho'] .= $tamanho->title.'|';
			}

			// remove o ultimo |
			$this->array['tamanho'] = substr($this->array['tamanho'], 0, -1);

			// Coloca marca
			$this->array['marca'] = '';

			// Coloca loja
		}
	}