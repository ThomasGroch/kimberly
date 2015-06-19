<?php
	require __DIR__ . '/zanox.php';

	Class Amaro extends Zanox{

		private $categorias_validas = array(
								'Moda Feminina',
				);

		public function Amaro($array){
			parent::__construct($array);
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
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			$html = parent::prepare();

			// Disponibilidade de estoque
			// if( $html->find('div.size-option--available') ){
			// 	$this->logger->info('[Skip] Sem Disponibilidade de estoque');
			// 	return false;
			// }

			// Obtem tamanho
			$this->produto['tamanho'] = '';
			$tamanhos = '';
			foreach($html->find('div.size-option--available') as $tamanho){
				$tamanhos .= $tamanho->title.'|';
			}
			// Remove o ultimo |
			$tamanhos = substr($tamanhos, 0, -1);
			// Remove dublicados
			$tamanhos = explode('|', $tamanhos);
			$tamanhos = array_unique($tamanhos);
			$this->produto['tamanho'] = implode('|', $tamanhos);

			// Obtem cor
			$i=0;
			$this->produto['cor'] = '';
			foreach($html->find('div.color-selection') as $selecao){
				if($i==0){
					foreach($selecao->find('img.color-option') as $cor){
						$this->produto['cor'] .= $cor->title.'|';
					}
				}
				$i++;
			}
			// Remove o ultimo |
			$this->produto['cor'] = substr($this->produto['cor'], 0, -1);


			// Obtem marca
			$this->produto['marca'] = '';

			// Obtem loja

			// Produto Tratado com sucesso
			return true;
		}
	}