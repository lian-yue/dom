<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-19 09:46:35
/*	Updated: UTC 2015-06-22 08:54:00
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use Countable, IteratorAggregate, ArrayIterator;
class Document extends Base implements IteratorAggregate, Countable{

	protected $conditions = [];


	public function __construct($document = false) {
		$document && $this->process($document);
	}


	public function __toString() {
		if (!$this->conditions) {
			return 'url-prefix("")';
		}
		$conditions = [];
		foreach ($this->conditions as $condition) {
			 $conditions[] = $condition[0] . '("'. (strcasecmp($condition[0], 'regexp') === 0 ? str_replace('"\\', '', $condition[1]) : $this->_regexp(str_replace('"', '\\"', $condition[1]))) .'")';
		}
		return implode(', ', $conditions);
	}


	private function _regexp($string) {
		$offset = 0;
		$length = strlen($string);

		while (($offset = $offset + strcspn($string, '\\"', $offset)) < $length) {
			// 匹配到了 "
			if ($string{$offset} === '"') {
				return;
			}

			// 跳到下一个去
			++$offset;

			// 下一个不存在 返回
			if (!isset($string{$offset})) {
				return;
			}

			// 跳过转义的那一个 " 比如
			++$offset;
		}
		return $string;
	}




	protected function prepare($document) {
		$conditions = [];
		while ($this->search(',') !== false) {
			$conditions[] = $this->buffer;
			$this->buffer = '';
		}
		$conditions[] = $this->buffer;
		foreach ($conditions as $condition) {
			if (preg_match('/^\s*(url|url\-prefix|domain|regexp)\s*\(("|\')?(.*)(?(2)\2|)\)\s*$/i', $condition, $matches)) {
				$this->conditions[] = [strtolower($matches[1]), empty($matches[2]) ? $matches[3] : (str_replace('\\'. $matches[2], $matches[2], $matches[3]))];
			}
		}
	}


	public function getIterator() {
		return new ArrayIterator($this->conditions);
	}

	public function count() {
		return count($this->conditions);
	}
}