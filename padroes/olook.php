<?php
	
	require __DIR__ . '/zanox.php';

	Class Olook extends Zanox{


		private $categorias_validas = array();

		public function Amaro($array = array()){
			parent::__construct($array);
		}

		/*
		* Funcao para retornar url do sistema de afiliados
		* sem o numero de paginacao
		*/
		public function getUrl() {
			return 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=12283&items=500&page=';
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