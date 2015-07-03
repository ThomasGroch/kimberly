<?php
	require __DIR__ . '/cityads.php';

	Class Ufcstore extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjQwNDc1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array(
										'Camisetas e camisas',
										'Chapéus',
										'Casacos',
										'Chortes',
										'Uniforme',
										'Tops',
										'Camisas',
										'Blusas',
										'Saias',
										'Macacões',
										'Bolsas',
										'Suéteres e cardigans'
										);
		
		var $black_list_category = array();


		public function Ufcstore(){
			parent::__construct();
		}

		public function setProduct($produto){
			parent::setProduct($produto);
			$this->produto['loja'] = 'UFC Store';
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		// Nessa loja todos dados extras sempre estao disponiveis
		// entao, caso nao encontre algum dado do produto ele sera descartado
		public function prepare(){

			$titulo = $this->produto['name'];
			if( stripos($titulo, 'Anos') OR
				stripos($titulo, 'Meses') OR
				stripos($titulo, 'Infantil')
			 ) {
				$this->logger->info('['.PADRAO.'][Skip] Produto rejeitado pelo titulo');
				return false;
			}

			if( ! parent::prepare() ){ return false; }
			

			$json = get_string_between($this->html_raw, 'init({', '})');
			$json = '{'.$json;

			$json = str_replace('$(', "'$(", $json);
			$json = str_replace(')', ")'", $json);
//			$json = get_string_between($json, 'items:', '                      ]');
			$json = trim($json);
			$json = substr($json, 1, -1);
			
			$json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
			
			//$json = preg_replace('/\s(?=([^"]*"[^"]*")*[^"]*$)/', '', $json);
			$json = my_json_decode($json);
			var_dump($json);
			exit;

			// Checar disponibilidade
			// Botao de comprar esta na pagina?
			if( ! $this->html->find('input#btBuy', 0) ) {
				// Indisponivel
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}
			
			// Obtem tamanho
			if( ! $this->getSize() ){
				return false;
			}

			// Obtem descricao
			$descricao = $this->html->find('div.boxDescription',0)->plaintext;
			$descricao = trim($descricao);
			$descricao = str_replace('  ', '', $descricao);

			$this->produto['descricao'] = html_entity_decode($descricao);

			// Produto Tratado com sucesso
			return true;
		}

		public function getSize() {
			$tamanho = '';
			$json_size = get_string_between($this->html, "type: 'size',", ' ]');
			if( $json_size ){
				// Produto tem tamanho
				$json_size = get_string_between($json_size, "items: ");
				$json_size = $json_size . ']';
				$size_arr = my_json_decode($json_size);

				foreach( $size_arr as $size) {
					if( ! isset($size['available'] ) ){
						$tamanho .= trim($size['label'] ) . '|';
					}
				}
				$tamanho = substr($tamanho, 0, -1);

				if(empty($tamanho)) {
					// Sem estoque disponivel para nenhum dos tamanhos
					$this->logger->info('['.PADRAO.'][Skip] Produto rejeitado, sem estoque para nenhum tamanho');
					return false;
				}
			}
			$this->produto['tamanho'] = $tamanho;
		}

		public function getCategory(){
			return $this->produto['category'];
		}
	}