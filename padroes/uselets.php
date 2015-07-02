<?php
	require __DIR__ . '/cityads.php';	

	class Uselets extends Cityads{

		var $xml_url = 'http://api.cityads.com/api/rest/webmaster/xml/goods?remote_auth=e2f6b2dd8b899aed22134a3602d3fe27&filter=NDQ5NjM5NzI1&limit=1000&start=';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();

		var $palavra_restrita = array('infantil', 'inf', 'infanto');

		public function setProduct($produto){
			parent::setProduct($produto);
			$this->produto['loja'] = "Uselets";
		}

		public function setCategory(){
			$categoria = array();
			
			$bread_crumb = $this->html->find('div#breadcrumb', 0);
			if(!empty($bread_crumb)) {
				foreach($bread_crumb->find('a') as $value) {
					
					if($value->title != "Home"){
						$categoria[] = trim($value->title);
					}
				}
				$categoria = implode("|", $categoria);
				$categoria = ltrim($categoria,"|");
				$this->produto['categoria'] = $categoria;

			}else{

				$this->produto['categoria'] = "";
			}
		}

		public function getBrand(){
			return $this->produto['brand'];
		}

		public function getCategory(){
			return $this->produto['categoria'];
		}

		public function getSizeColor(){

			if ($this->html) {

				$open_json = '[{';
				$close_json = '}]';
				$cor = "";
				$tamanho = "";

				$script = array();
				$cores = array();
				$tamanhos = array();

				
				$script_p = $open_json . get_string_between($this->html->find('script',28), "=[{", "}]", 1) . $close_json;
				$script_p = json_decode($script_p, true);

				
				$script_m = $open_json . get_string_between($this->html->find('script',28), "=[{", "}]", 2) . $close_json;
				$script_m = json_decode($script_m, true);

				$script_g .= $open_json . get_string_between($this->html->find('script',28), "=[{", "}]", 3) . $close_json;
				$script_g = json_decode($script_g, true);
				
				$scripts []= $script_p;
				$scripts []= $script_m;
				$scripts []= $script_g;

				//echo '<pre>';print_r($scripts); die();
				if(count($scripts) > 0){

					foreach ($scripts as  $script) {
						foreach ($script as $value) {

							if ($value['stock']) {

								$cores[] = trim($value['color']);
								$tamanhos[] = trim($value['size']);
								
							}
						}
						//echo '<pre>';print_r($value);die();
					}
					$cores = array_unique($cores);
					$tamanhos = array_unique($tamanhos);

					$cor = implode("|", $cores);
					$cor = ltrim($cor,"|");
					
					$tamanho = implode("|", $tamanhos);
					$tamanho = ltrim($tamanho,"|");
				}

				$this->produto['cor'] = $cor;
				$this->produto['tamanho'] = $tamanho;
			}
		}


			/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		// Nessa loja todos dados extras sempre estao disponiveis
		// entao, caso nao encontre algum dado do produto ele sera descartado
		public function prepare(){

			if (procpalavras($this->produto['name'], $this->palavra_restrita)) {
				$this->logger->info('['.PADRAO.'][Skip] Produto sem interesse');
				return false;
			}

			if( ! parent::prepare() ){ return false; }
			
			if(!$this->html){
				$this->logger->info('['.PADRAO.'][Skip] HTML do produto nao disponivel');
				return false;
			}

			// Checar disponibilidade
			$div_estoque = $this->html->find('div#noStock',0);

			if(empty($div_estoque)){
				// Obtem cor e tamanho
				$this->getSizeColor();

				// Produto Tratado com sucesso
				return true;
			}else{
				$this->logger->info('['.PADRAO.'][Skip] Produto nao disponivel');
				return false;
			}			
		}
	}