<?php
	require __DIR__ . '/cityads.php';

	Class Sandromoscoloni extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM5NzM1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Sandromoscoloni(){
			parent::__construct();
			$this->loja = 'Sandro Moscoloni';
		}

		public function setProduct($produto){
			parent::setProduct($produto);

			// Trata o nome
			// $nome = explode('-', $this->produto['name'] );
			// $nome = array_slice($nome, 0,-1);
			// $nome = implode('-', $nome);
			// $this->produto['name'] = $nome;
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		// Nessa loja todos dados extras sempre estao disponiveis
		// entao, caso nao encontre algum dado do produto ele sera descartado
		public function prepare(){
			if( ! parent::prepare() ){ return false; }
			
			// Checar disponibilidade


			// Obtem cor
			$this->getColor();

			// Obtem marca
			if( ! $this->getBrand() ) {
				return false;
			}

			// Obtem tamanho
			$this->getSize();

			// print_r($this->produto);
			// exit;
			// Produto Tratado com sucesso
			return true;
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}
	}