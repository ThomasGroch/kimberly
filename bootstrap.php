<?php

	// função para carregar classes automaticamente
	function __autoload( $class ){
		// Garante apenas letras minúsculas no nome do arquivo de classe
		//$class = strtolower( $class );
		
		// Inclui a classe que precisamos
		include_once("{$class}.php");
	}