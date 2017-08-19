<?php

class DateVO {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.DateVO";
	
	public $date;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}