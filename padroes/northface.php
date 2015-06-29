<?php
	require __DIR__ . '/cityads.php';	

	class Northface extends Cityads{

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM4Nzk1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function NorthFace($array = array()){
			parent::__construct($array);
			$this->loja = 'North Face';
		}

		public function setProduct($produto){
			parent::setProduct($produto);
		}

		public function setCategory(){
			$categoria = array();
			if( !$this->html->find('ul.[itemprop="breadcrumb"]')) {
				continue;
			}

			foreach($this->html->find('ul.[itemprop="breadcrumb"]',0)->find('a') as $value) {
				if($value->title != "Por Categoria"){
					$categoria[] = $value->title;
				}
			}
			$categoria = implode("|", $categoria);
			$this->produto['categoria'] = $categoria;
			//echo '<pre>';print_r($categoria);
			/*$this->produto['categoria'] = $categoria;
			$this->produto['categoria'] = substr($this->produto['categoria'], 0, -1);
			$this->produto['categoria'] = trim($this->produto['categoria']);*/
		}

		public function getBrand(){
			return $this->produto['brand'];
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}

		public function getColor() {
			$cor = array();
			if ($this->html->find('div[data-codigoatributo="156"]')) {
				foreach ($this->html->find('div[data-codigoatributo="156"]',0)->find('div') as $value) {
					if( strpos($value->class, 'disabled') ){
						continue;
					}
					// Cor encontrada
					$cor[]= $value->getAttribute("data-valoratributo");
				}
			}
			$cor = implode("|", $cor);
			$this->produto['cor'] = $cor;
			
		}

		public function getSize() {
			$tamanho = array();
			$div_tamanho = $this->html->find('div[data-codigoatributo="157"]');
			if ( ! $div_tamanho ) {
				$this->logger->info('['.PADRAO.'][Warning] Nao foi possivel encontrar tamanho'.' '.$this->link_do_produto);
			}
			foreach ($this->html->find('div[data-codigoatributo="157"]',0)->find('div') as $value) {
				
				if(strpos($value->class, 'disabled') === FALSE){
					$tamanho[]= $value->plaintext;
				}
			}
			$tamanho = implode("|", $tamanho);
			$this->produto['tamanho'] = $tamanho;
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
			$div_estoque = $this->html->find('div.avisoIndisponivel',0)->style;
			if( ! isset($div_estoque) OR $div_estoque != 'display:none;') {
				//echo "aviso Indisponivel";
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}


			// Obtem cor
			$this->getColor();

			// Obtem tamanho
			$this->getSize();

			// Produto Tratado com sucesso
			return true;
		}
	}