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

		public function getColorByCode($colorId) {
			$li = $this->html->find('li#'.$colorId, 0);
			if( ! $li ){
				return false;
			}
			$cor_txt = $li->find('a', 0)->plaintext;
			$cor_txt = str_replace('+', ' ', $cor_txt);

			return $cor_txt;

			// <a href="?color=COR_1000">Marrom-Claro</a>
			//return $this->html->find('a[href="?color=' . $color_id . '"]', 0)->plaintext;
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
			var_dump($this->link_do_produto);
			$json = get_string_between($this->html, 'photos:', '], ').']';
			$json = my_json_decode($json);

			$cores = array();
			$tamanhos = array();
			foreach ($json as $cor) {
				foreach ($cor['sizes'] as $size) {
					if( $size['quant'] > 0 ){
						$color_txt = $this->getColorByCode( $cor['colorId'] );
						if( $color_txt ) {
							$cores[] = trim($color_txt);
						}
						$tamanhos[] = trim($size['val']);
					}
				}
			}
			$cores = array_unique($cores);
			$tamanhos = array_unique($tamanhos);

			$this->produto['cor'] = implode('|', $cores);
			$this->produto['tamanho'] = implode('|', $tamanhos);


			// Obtem descricao
			$descricao = $this->html->find('div#descricao',0)->plaintext;
			$descricao = trim($descricao);
			$descricao = str_replace('  ', '', $descricao);

			$this->produto['descricao'] = html_entity_decode($descricao);


			print_r($this->produto);
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

		public function setCategory(){
			$this->produto['categoria'] = $this->produto['category'];
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}
	}