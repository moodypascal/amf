<?php

class FurnitureDataVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.FurnitureDataVO";
	
	public $parameters;
	public $x;
	public $y;
	public $rot;
	public $uid;
	public $id;
	public $type;
	public $active;
	public $premium;
	public $bought;
	public $roomID;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}