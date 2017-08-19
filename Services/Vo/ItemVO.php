<?php

class ItemVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.ItemVO";
	
	public $id;
	public $name;
	public $type;
	public $price;
	public $zettSort;
	public $premium;
	public $bought;
	public $active;
	public $movementType;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}