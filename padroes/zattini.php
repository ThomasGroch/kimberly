<?php
	require __DIR__ . '/zanox.php';

	Class Zattini extends Zanox{

		public $black_list_categories = array('Infantil');

		public function Zattini($array = array()){
			parent::__construct($array);
			$this->loja = 'zattini';
		}

		public function setCategory(){
			parent::setCategory();
			$categorias = explode('|', $this->produto['categorias']);
			if( !empty($categorias) ){
				foreach($categorias as $key => $cat){
					$categorias[$key] = str_replace(' Brownshoes', '', $cat);
				}
				$this->produto['categorias'] = $categorias;
			}
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
		public function getXmlUrl() {
			return 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=15900&items=500&page=';
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			$html = parent::prepare();
			
			// Obtem tamanho
			// <span class="attr-name unavailable">
			$tamanhos = '';
			$tamanho_spans = $html->find('span.attr-name');
			if( ! $tamanho_spans ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi foi possivel encontrar tamanhos');
			}

			foreach( $tamanho_spans as $span){
				if( strpos($span->class, 'unavailable') ){
					continue;
				}
				$tamanhos .= trim($span->plaintext).'|';
			}
			$tamanhos = substr($tamanhos, 0, -1);
			$this->produto['tamanhos'] = $tamanhos;


			// Obtem cor
			// <a href="?color=138" class="attr-name unavailable"
			$cores = '';
			$cor_as = $html->find('a.attr-name');
			if( ! $cor_as ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi foi possivel encontrar cores');
			}

			foreach( $cor_as as $a){
				if( strpos($a->class, 'unavailable') ){
					continue;
				}
				$cores .= trim($a->plaintext).'|';
			}
			$cores = substr($cores, 0, -1);
			$this->produto['cores'] = $cores;

			// Obtem marca
			$this->produto['marca'] = 'Zattini';

			// Produto Tratado com sucesso
			return true;
		}
	}