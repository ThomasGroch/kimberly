<?php
	require __DIR__ . '/zanox.php';

	Class Glamour extends Zanox{

		var $xml_url = 'http://api.zanox.com/xml/2011-03-01/products/?connectid=089EAF947B7A0B3C896E&adspace=1916212&programs=15305&items=500&page=';
		
		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();

		public function Glamour($array = array()){
			parent::__construct($array);
		}

		/*
		* Funcao para capturar dados extras
		* Entrada: array de um produto
		* Saida: array de um produto + campos extras
		*/
		public function prepare(){
			parent::prepare();
			
			// Obtem marca
			$html_res = $this->html->find('p.brand', 0);
			if( $html_res ){
				$marca = $html_res->find('a',0)->plaintext;
				$marca = trim($marca);
			}
			if( empty($marca)){
			 	$this->logger->info('[Skip] Nao foi foi possivel encontrar marca');
			 	return false;
			}
			$this->produto['marca'] = $marca;

			// Obtem tamanho
			$tamanho_script = $this->html->find('head',0);
			if( ! empty($tamanho_script)){
				$json = $tamanho_script->last_child();
				$json = get_string_between($json, '<script>var skuJson_0 = ', ';CATALOG_SDK.');
				$json = json_decode($json, true);

				foreach ($json['skus'] as $key => $sku) {
					if( $sku['available'] ){
						$tamanhos[] = $sku['dimensions']['Tamanho'];
					}
				}
				$this->produto['tamanho'] = implode('|', $tamanhos);
			}
			// Se nao encontrar um tamanho coloca como tamanho unico
			$this->produto['tamanho'] = ( empty($this->produto['tamanho']) ) ? 'U' : $this->produto['tamanho'];

			// Nao foi implementado as subcategorias

			// Produto Tratado com sucesso
			return true;
		}
	}