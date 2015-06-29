<?php
	require __DIR__ . '/zanox.php';

	class Rayban extends Zanox{

		var $xml_url = '';

		var $produto = array();

		var $link_produto = '';
		
		var $white_list_category = array();
		
		var $black_list_category = array();		

		// código de afiliado do tagbox
		public $affiliate_id = '1916212';
		public $site_name = 'Tag Box';

		public function setProduct($produto){

			if(!empty($produto)){
				$this->produto = $produto;
				$link_produto = str_replace("[AFFILIATE_ID]", $this->affiliate_id, $produto['trackingLinks']['trackingLink']['ppc']);	
				$link_produto = str_replace("[AFFILIATE/SITE_NAME]", $this->site_name, $produto['trackingLinks']['trackingLink']['ppc']);	
				
				$this->produto['trackingLinks']['trackingLink']['ppc'] = $link_produto;
			}
		}
	}