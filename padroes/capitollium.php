<?php
	require __DIR__ . '/cityads.php';

	Class Capitollium extends Cityads {

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM4MDM1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();


		public function Capitollium(){
			parent::__construct();
			$this->loja = 'Capitollium';
		}

		public function setProduct($produto){
			parent::setProduct($produto);

			// Trata o nome
			$nome = explode('-', $this->produto['name'] );
			$nome = array_slice($nome, 0,-1);
			$nome = implode('-', $nome);
			$this->produto['name'] = $nome;
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
			}else{
				
				foreach ($this->html->find('div[data-codigoatributo="157"]',0)->find('div') as $value) {
					if( strpos($value->class, 'disabled') !== false ) {
						continue;
					}
					// Tamanho encontrado
					$tamanho .= trim($value->plaintext).'|';
				}
				
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
			$categoria = '';
			if( ! $this->html->find('ul.[itemprop="breadcrumb"]')) {
				continue;
			}
			foreach($this->html->find('ul.[itemprop="breadcrumb"]',0)->find('span') as $value) {
				$categoria .= trim($value->plaintext).'|';
			}
			$this->produto['categoria'] = $categoria;
			$this->produto['categoria'] = substr($this->produto['categoria'], 0, -1);
			$this->produto['categoria'] = trim($this->produto['categoria']);
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}
	}