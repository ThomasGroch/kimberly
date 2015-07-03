<?php
	require __DIR__ . '/cityads.php';

	Class Renner extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjQwMDY1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Renner(){
			parent::__construct();
		}

		public function setProduct($produto){
			parent::setProduct($produto);
			$this->produto['loja'] = 'Renner';
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
			
			$titulo = $this->produto['name'];
			if( stripos($titulo, 'Anos') OR
				stripos($titulo, 'Meses') OR
				stripos($titulo, 'Infantil')
			 ) {
				$this->logger->info('['.PADRAO.'][Skip] Produto rejeitado pelo titulo');
				return false;
			}

			// Checar disponibilidade
			// Obtem tamanho
			$this->getSize();

			// Obtem cor
			$this->getColor();

			// Obtem Marca
			//$marca = get_string_between($this->html, 'pageProductBrand":"', '","');
			//$this->produto['marca'] = $marca;
			
			// Produto Tratado com sucesso
			return true;
		}


		public function getColor() {
			$cor = '';
			$color_div = $this->html->find('div.skuColorsList', 0);
			if ( $color_div ) {
				foreach( $color_div->find('label[class="inptRadio"]') as $label ){
					// Cor encontrada
					$cor .= trim($label->name).'|';
				}
			}
			$this->produto['cor'] = $cor;
			$this->produto['cor'] = substr($this->produto['cor'], 0, -1);
		}

		public function getSize() {
			$tamanho = '';

			// Caso o produto seja um bolsa/perfume nao encontrara essa tag
			// entao deixa sem tamanho
			$div_sizes = $this->html->find('div.skuSizeList',0);
			if( $div_sizes ) {
				// Produto com tamanhos
				foreach( $div_sizes->find('label[class="inptRadio"]') as $label ){
					if( strpos($label->class, 'soldOut') ) {
						continue;
					}
					$tamanho .= trim($label->name).'|';
				}
			}
			$this->produto['tamanho'] = $tamanho;
			$this->produto['tamanho'] = substr($this->produto['tamanho'], 0, -1);
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}
	}