<?php

class AmfResponse {
	
	public $_explicitType = "com.pandaland.mvc.model.vo.AmfResponse";
	
	public $statusCode;
	public $message;
	public $valueObject;
	
	public function __set($name, $value) {
		$this->$name = $value;
	}
	
	public function __get($name) {
		return $this->$name;
	}
	
}