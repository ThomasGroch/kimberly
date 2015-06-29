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
				$this->xml_em_array = $array;
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

	}