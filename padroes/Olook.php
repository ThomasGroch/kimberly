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


			// Obtem cor
			$i=0;
			$this->produto['cor'] = '';
			foreach($html->find('div.colors') as $selecao){
				if($i==0){
					foreach($selecao->find('img.color-option') as $cor){
						$this->produto['cor'] .= $cor->title.'|';
					}
				}
				$i++;
			}

			echo '<pre>';print_r($this->produto);


		}

	}