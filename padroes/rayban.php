<?php
	require __DIR__ . '/zanox.php';

	class Rayban extends Zanox{

	

		public function setProduto($produto){

			if(!empty($produto)){
				$this->produto = $produto;
				$link_produto = str_replace("[AFFILIATE_ID]", $this->affiliate_id, $produto['trackingLinks']['trackingLink']['ppc']);	
				$link_produto = str_replace("[AFFILIATE/SITE_NAME]", $this->site_name, $produto['trackingLinks']['trackingLink']['ppc']);	
				
				$this->produto['trackingLinks']['trackingLink']['ppc'] = $link_produto;
			}
		}
	}