<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-05-25 13:03:30
/*	Updated: UTC 2015-06-14 08:16:09
/*
/* ************************************************************************** */
namespace Loli\DOM;
use IteratorAggregate, ArrayAccess, Serializable, Countable, JsonSerializable, ArrayIterator;
class Attributes implements IteratorAggregate, ArrayAccess, Serializable, Countable, JsonSerializable{

	private $_attributes = [];


	public function __construct(array $attributes = []) {
		foreach ($attributes as $key => $value) {
			$this->offsetSet($name, $value);
		}
	}

	public function __get($name) {
		return  $this->offsetGet($name);
	}

	public function __set($name, $value) {
		$this->offsetSet($name, $value);
	}

	public function __isset($name) {
		return  $this->offsetExists($name);
	}

	public function __unset($name) {
		return  $this->offsetUnset($name);
	}

	public function offsetSet($name, $value) {
		$name = strtolower(trim($name));
		if (!$name || $value === NULL || $value === false) {
			unset($this->_attributes[$name]);
		} else {
			$this->_attributes[$name] = $value;
		}
	}
	public function offsetExists($name) {
		return isset($this->_attributes[$name]);
	}
	public function offsetUnset($name) {
		unset($this->_attributes[$name]);
	}

	public function offsetGet($name) {
		return isset($this->_attributes[$name]) ? $this->_attributes[$name] : NULL;
	}

	public function serialize() {
		return serialize($this->_attributes);
	}

	public function unserialize($attributes) {
		$this->_attributes = unserialize($attributes);
	}

	public function count() {
		return count($this->_attributes);
	}

	public function getIterator() {
		return new ArrayIterator($this->_attributes);
	}



	public function jsonSerialize() {
		$array = [];
		foreach ($this as $key => $value) {
			$array[$key] = $value;
		}
		return $array;
	}

	public function __toString() {
		$array = $this->jsonSerialize();
		ksort($array);
		$attributes = '';
		foreach ($array as $name => $value) {
			$attributes .= ' ' . $name . ($value === true ? '' : '="' . str_replace('"', '&quot;', $value) . '"');
		}
		return $attributes;
	}

}