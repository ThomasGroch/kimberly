<?php
	require __DIR__ . '/cityads.php';

	Class Timberland extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM3Njc1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Timberland(){
			parent::__construct();
			$this->loja = 'Timberland';
		}

		public function setProduct($produto){
			parent::setProduct($produto);
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		// Nessa loja todos dados extras sempre estao disponiveis
		// entao, caso nao encontre algum dado do produto ele sera descartado
		public function prepare(){
			parent::prepare();
			
			// Checar disponibilidade
			$json = get_string_between($this->html, 'photos:', '], ').']';
			//$json = json_decode($json, true);
			var_dump($json);

			exit;
			if( ! isset($div_estoque) OR $div_estoque != 'display:none;') {
				//echo "aviso Indisponivel";
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}


			// // Obtem cor
			// $this->getColor();

			// // Obtem marca
			// if( ! $this->getBrand() ) {
			// 	return false;
			// }

			// // Obtem tamanho
			// if( ! $this->getSize() ){
			// 	return false;
			// }

			// print_r($this->produto);
			// exit;
			// Produto Tratado com sucesso
			return true;
		}


		public function getColor() {
			$cor = '';
			if ($this->html->find('div[data-codigoatributo="158"]')) {
				foreach ($this->html->find('div[data-codigoatributo="158"]',0)->find('div') as $value) {
					if( strpos($value->class, 'disabled') ){
						continue;
					}
					// Cor encontrada
					$cor .= trim($value->getAttribute('data-valoratributo')).'|';
				}
			}
			$this->produto['cor'] = $cor;
			$this->produto['cor'] = substr($this->produto['cor'], 0, -1);
		}

		public function getSize() {
			$tamanho = '';
			$div_tamanho = $this->html->find('div[data-codigoatributo="157"]');
			if ( ! $div_tamanho ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi possivel encontrar tamanho'.' '.$this->link_do_produto);
				return false;
			}
			foreach ($this->html->find('div[data-codigoatributo="157"]',0)->find('div') as $value) {
				if( strpos($value->class, 'disabled') !== false ) {
					continue;
				}
				// Tamanho encontrado
				$tamanho .= trim($value->plaintext).'|';
			}
			$this->produto['tamanho'] = $tamanho;
			$this->produto['tamanho'] = substr($this->produto['tamanho'], 0, -1);
		}

		public function getBrand() {
			$marca = '';
			// <div class="produtoInfo half right">
			// <div onclick="javascript:window.open('http://www.capitollium.com.br/fabricante/amissima')" class="fabricante"
			$div = $this->html->find('div.fabricante', 0);
			$marca = get_string_between($div->onclick, '.com.br/fabricante/', "')");
	
			if ( !isset($div->onclick) ) {
				$this->logger->info('['.PADRAO.'][Skip] Nao foi possivel encontrar marca'.' '.$this->link_do_produto);
				return false;
			}
			$this->produto['marca'] = $marca;
			return true;
		}

		public function setCategory(){
			$this->produto['categoria'] = $this->produto['category'];
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}
	}