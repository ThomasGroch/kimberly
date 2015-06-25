<?php
	require __DIR__ . '/zanox.php';

	Class Amaro extends Zanox{

		private $categorias_validas = array(
				);

		public function Amaro($array = array()){
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
		* Funcao para retornar url do sistema de afiliados
		* sem o numero de paginacao
		*/
		public function getUrl() {
			return 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=13521&items=500&page=';
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			$html = parent::prepare();
			
			// Obtem tamanho
			if( ! $html->find('div.size-option--available') ) {
				$this->logger->info('['.PADRAO.'][Skip] Nao foi foi possivel encontrar tamanhos');
				return false;
			}

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
			$this->produto['marca'] = 'Amaro';

			// Produto Tratado com sucesso
			return true;
		}
	}