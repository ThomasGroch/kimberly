<?php

	Abstract class Loja{

		protected $array = array();
		protected $produto = array();
		protected $link_do_produto;

		public function Loja($array = array()){
			/*
			* Traz instancia do logger para uso na classe
			*/
			global $logger;
			$this->logger = $logger;

			if( !empty($array) ){
				$this->array = $array;
			}
		}

		public function getLastPage(){}

		public function setProduto($produto){
			$this->produto = $produto;
		}

		public function setUrl() {}
		
		public function getUrl() {}

		public function setLoja(){

			$this->produto['loja'] = get_class($this);
		}

		public function getLoja(){

			return $this->produto['loja'];
		}

		public function getProductsList(){}

		public function validate(){}

		public function prepare(){
			// Obtem html
			$html = str_get_html(get_content($this->link_do_produto));

			if( ! $html ){
				$this->logger->info('[Skip] HTML invalido');
				return false;				
			}

			// Html retornou vazio?
			if( $this->html->plaintext == '' ) {
				$this->logger->info('[Skip] Html vazio > '.$this->link_do_produto);
				return false;
			}

			$this->getLoja();

			return $html;
		}
	}