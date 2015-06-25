<?php
class Contador {
	
	static $contadores = array();

	static function add($group = ''){
		if( ! isset( self::$contadores[$group]) ){
			self::$contadores[$group] = 0;
		}
		self::$contadores[$group]++;
	}

	static function write($group = ''){
		if( ! isset( self::$contadores[$group]) ){
			self::$contadores[$group] = 0;
		}
		return $group.': '.self::$contadores[$group].' ';
	}
}