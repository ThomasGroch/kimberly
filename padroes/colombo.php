<?php
	
	require __DIR__ . '/zanox.php';

	Class Colombo extends Zanox{

		private $html;
		private $categorias_validas = array();

		public function Colombo($array = array()){
			parent::__construct($array);
		}

		public function setHtml(){

			$this->html = parent::prepare();
		}

		public function setCor(){
			$this->produto['cor'] = $this->getCorHtml();
		}

		public function setTamanho(){
			$this->produto['tamanho'] = $this->getTamanhoHtml();
		}

		public function setMarca(){

			$this->produto['marca'] = 'Colombo';	
		}

		public function setCategoria(array $categoria){

			if(is_array($categoria)){

				$this->produto['categoria'] = implode("|", $categoria);
			}
		}

		/*
		* Funcao para retornar url do sistema de afiliados
		* sem o numero de paginacao
		*/
		public function getUrl() {
			return 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=15596&items=500&page=';
					
		}

		/**
		 *	Método que retorna a cor do produto caso encontre.
		 *		
		*/
		public function getCorHtml(){
			$cor = "";
			if ($this->html)
				$cor = $this->html->find('td.Cor',0)->plaintext;	
			
			return $cor;
		}


		/**
		 * Método que retorna os tamanhos do produto. 	
		*/
		public function getTamanhoHtml(){

			$i = 0;
			$tamanhos = "";
			$arr = array();
			$script = "";

			if ($this->html){

				$script = $this->html->find('head',0)->find('script',27);
				$script = str_replace('<script>var skuJson_0 = ', '', $script);
				$script = str_replace(';CATALOG_SDK.setProductWithVariationsCache(skuJson_0.productId, skuJson_0); var skuJson = skuJson_0;</script>', '', $script);

				$arr = json_decode($script, true);

				if(count($arr) > 0){
					foreach ($arr['skus'] as $key) {
						if($key['available']){
							$tamanhoNoArray = $key['dimensions']['Tamanho'];
							$size = str_replace('/ ', '|', $tamanhoNoArray);
							if($i==0){	
								$tamanhos = $size;
								$i++;
							}else{
								$tamanhos .= '|'.$size;
							}			
						}
					}
				}
			}	
			return $tamanhos;
		}

		/**
		 * Obtem cas categorias do produto.
		*/
		public function getCategoriaHtml(){

			$categoria = array();
			if($this->html){
				$breadCrumb = $this->html->find('div.bread-crumb',0);

				if(!empty($breadcrumb)){
					foreach ($breadcrumb->find('a') as $value) {
						if( strcmp($value->plaintext, "Camisaria Colombo") != 0)
							$categoria[] = $value->plaintext;		
							
					}
					
				}		
			}
			return $categoria;
		}
		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){

			$this->setHtml();

			$categoria = $this->getCategoriaHtml();
			
			if(is_array($categoria) && array_search("Infantil", $categoria) != false){
				$this->logger->info('[Skip] Categoria não válida > '.$this->link_do_produto);
				return false;
			}

			//Verifica se o produto está indisponível.
			if($this->html->find('p.unavailable-button',0)->style != 'display:none'){ 
				$this->logger->info('[Skip] Produto esgotado > '.$this->link_do_produto);
				return false;
			}

			$this->setTamanho();
			$this->setCor();
			$this->setMarca();
			$this->setCategoria($categoria);

			// Produto Tratado com sucesso
			return true;
		}

	}