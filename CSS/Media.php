<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-15 13:12:41
/*	Updated: UTC 2015-07-20 11:49:56
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use ArrayIterator, Countable, IteratorAggregate;
class Media implements IteratorAggregate, Countable{

	protected $conditions = [];


	public function __construct($media = false) {
		if ($media) {
			foreach (explode(',', preg_replace('/\/\*.*?(\*\/|$)/', '', $media)) as $value) {
				if ($value = trim($value)) {
					$condition = new MediaCondition($value);
					$this->conditions[] = $condition;
				}
			}
		}
	}

	public function __toString() {
		return $this->conditions ? implode(', ', $this->conditions) : 'all';
	}


	public function insertMedia(MediaCondition $media, $index = NULL) {
		if ($index === NULL) {
			$this->conditions[] = $media;
		} else {
			array_splice($this->conditions, $index, 0,[$media]);
		}
		return $media;
	}

  	public function deleteMedia(MediaCondition $media) {
  		if (($index = array_search($media, $this->conditions, true)) !== false) {
			unset($this->conditions[$index]);
			$this->conditions = array_values($this->conditions);
		}
		return $media;
  	}

	public function getIterator() {
		return new ArrayIterator($this->conditions);
	}

	public function count() {
		return count($this->conditions);
	}
}