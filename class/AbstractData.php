<?php

abstract class AbstractData {

	protected $data = [];

	public function setData($data)
	{	
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}