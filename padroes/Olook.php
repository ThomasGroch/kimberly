<?php
	
	require __DIR__ . '/zanox.php';

	Class Olook extends Zanox{


		private $categorias_validas = array();

		public function Amaro($array){
			parent::__construct($array);
		}
		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){

			$html = parent::prepare();

			//Verifica se o produto está indisponível.
			if($html->find('p.out')){ 
				return false;
			}

			// Obtem tamanho
			$this->produto['tamanho'] = '';
			$tamanhos = '';
			foreach($html->find('div.size', 0)->find('label') as $tamanho){
				$tamanhos .= $tamanho->plaintext.'|';
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
			
			foreach($html->find('ol.colors', 0)->find('img')  as $cor){
				
				$this->produto['cor'] .= $cor->title.'|';	
				
			}

			// Remove o ultimo |
			$this->produto['cor'] = substr($this->produto['cor'], 0, -1);
			// Obtem marca
			$this->produto['marca'] = 'Olook';

			// Produto Tratado com sucesso
			return true;
		}

	}