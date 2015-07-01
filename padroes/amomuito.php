<?php
	require __DIR__ . '/cityads.php';

	Class Amomuito extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM5OTA1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Amomuito(){
			parent::__construct();
			$this->loja = 'Amo Muito';
			$this->marca = 'Amo Muito';
		}

		public function setProduct($produto){
			parent::setProduct($produto);
			unset( $this->produto['text'] );
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
			$p_estoque = $this->html->find('p#quantity_wanted_p', 0)->style;
			if( $p_estoque == 'display: none;' ) {
				// Indisponivel
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}

			// Obtem descricao
			$descricao = $this->html->find('div[id="maisinfo"]',0)->plaintext;
			$descricao = trim($descricao);
			$descricao = str_replace('DETALHES DO PRODUTO', '', $descricao);
			$descricao = trim($descricao);
			$descricao = str_replace('  ', '', $descricao);
			
			$this->produto['descricao'] = $descricao;

			// Produto Tratado com sucesso
			return true;
		}

		public function getCategory(){
			return $this->produto['category'];
		}
	}