<?php

class ListVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.ListVO";
	
	public $list = array();
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}