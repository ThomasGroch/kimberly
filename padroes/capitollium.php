<?php
	require __DIR__ . '/zanox.php';

	Class Capitollium extends Zanox{

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM4MDM1';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();

		var $single_page = TRUE;


		public function Capitollium($array = array()){
			parent::__construct($array);
			$this->loja = 'capitollium';
		}

		public function setProduct($produto){
			$produto['url'] = 'http:'.$produto['url'];
			$this->produto = $produto;
		}

		public function getProductsList(){
			if( !isset($this->xml_em_array['data']['items']['item']) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos nao esta no XML > '. json_encode($this->xml_em_array) );
				return false;
			}
			$product_list = $this->xml_em_array['data']['items']['item'];
			if ( empty($product_list) ) {
				$this->logger->info('['.PADRAO.'][Skip] Lista de produtos esta vazia > '. json_encode($this->xml_em_array) );
				return false;
			}
			return $product_list;
		}

		public function getProductUrl() {
			$url = $this->produto['url'];
			$html = str_get_html(get_content($url));

			$a = $html->find('a',0);
			$link_verdadeiro = $a->href;
			if( ! $link_verdadeiro ) {
				$this->logger->info('['.PADRAO.'][DEBUG] Nao achei o link verdadeiro: '.$url);
			}
			$url2 = 'http://cityadspix.com'.$link_verdadeiro;
			
			$html2 = str_get_html(get_content($url2));

			$url3 = $html2->find('a',0)->href;

			return $url3;
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
			$div = $this->html->find('div.avisoIndisponivel', 0);
			if( $div->style != 'display:none;') {
				//echo "aviso Indisponivel";
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}

			// Obtem cor
			if( $this->getColor() ) {
				return false;
			}

			// Obtem tamanho
			if( $this->getSize() ) {
				return false;
			}

			// Obtem marca
			if( $this->getSize() ) {
				return false;
			}

			// Trata o nome
			$nome = explode('-', $this->produto['name'] );
			$nome = array_slice($nome, 0,-1);
			$nome = implode('-', $nome);
			$this->produto['name'] = $nome;

			print_r($this->produto['marca']);
			exit;
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
					$cor .= $value->getAttribute('data-valoratributo').'|';
				}
			}
			$this->produto['cor'] = $cor;
			$this->produto['cor'] = substr($this->produto['cor'], 0, -1);
		}

		public function getSize() {
			$tamanho = '';
			if ($this->html->find('div[data-codigoatributo="157"]')) {
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
			var_dump($div->onclick);
			exit;
			$this->logger->info('['.PADRAO.']'.$div->onclick);

			// if ( $div ) {
			// 	//$marca = get_string_between('.com.br/fabricante/', $div->onclick, "')");
				
			// 	if( empty($marca) ) {
			// 		$this->logger->info('['.PADRAO.'][Skip] Nao foi foi possivel encontrar marca'.' '.$this->link_do_produto);
			// 		return false;
			// 	}
			// }
			$this->produto['marca'] = $marca;
		}

		public function setCategory(){
			$categoria = '';
			if( ! $this->html->find('ul.[itemprop="breadcrumb"]')) {
				continue;
			}
			foreach($this->html->find('ul.[itemprop="breadcrumb"]',0)->find('span') as $value) {
				$categoria .= $value->plaintext.'|';
			}
			$this->produto['categoria'] = $categoria;
			$this->produto['categoria'] = substr($this->produto['categoria'], 0, -1);
		}

	}