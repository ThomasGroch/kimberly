<?php
	require __DIR__ . '/cityads.php';

	Class Wqsurf extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM5NzQ1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Wqsurf(){
			parent::__construct();
			$this->loja = 'WQ Surf';
			$this->marca = 'WQ Surf';
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
			$link_estoque = $this->html->find('link[itemprop="availability"]', 0);
			if( ! $link_estoque OR 
				! $link_estoque->find('span')->plaintext != 'Em estoque' ) {
				// Indisponivel
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}

			// Obtem tamanhos
			$this->getSize();

			// Obtem descricao
			$descricao = $this->html->find('span[itemprop="description"]',0)->plaintext;
			$descricao = trim($descricao);
			$descricao = str_replace('  ', '', $descricao);
			
			$this->produto['descricao'] = html_entity_decode($descricao);

			// Produto Tratado com sucesso
			return true;
		}

		public function getSize() {
			$tamanho = '';
			$json_size = get_string_between($this->html, '"options":', '}]}');
			if( $json_size ){
				// Produto tem tamanho
				$json_size = $json_size.'}]';
				$size_arr = json_decode($json_size);
				foreach( $size_arr as $size_obj) {
					$tamanho .= trim($size_obj->label) . '|';
				}
				$tamanho = substr($tamanho, 0, -1);
			}
			$this->produto['tamanho'] = $tamanho;
		}


		public function getCategory(){
			return $this->produto['category'];
		}
	}