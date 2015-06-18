<?php

	Class Zanox{

		private $objXml;


		function Zanox($xml, $url = false){

			if(!empty($xml)){

				$this->objXml = new SimpleXMLElement($xml, LIBXML_NOEMPTYTAG, $url);
			}

		}

		public function iterarXMl(){

			$arrayXml = array();
			$arrayXml = json_encode($this->objXml);
			$arrayXml = json_decode($arrayXml);
		}


	}