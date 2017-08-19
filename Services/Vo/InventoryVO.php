<?php

class InventoryVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.InventoryVO";
	
	public $activeItems = array();
	public $inactiveItems = array();
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}