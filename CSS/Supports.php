<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-19 11:57:03
/*	Updated: UTC 2015-06-22 15:26:14
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use IteratorAggregate, ArrayIterator, Countable;

class Supports extends Base implements IteratorAggregate, Countable{

	const TYPE_GROUP = 1;

	const TYPE_VALUE = 2;

	const NESTING = 10;

	protected $type;

	protected $value;

	protected $parent;

	protected $childs = [];

	public function __construct($value = false, $type = self::TYPE_GROUP) {
		switch ($type) {
			case self::TYPE_VALUE:
				$this->type = self::TYPE_VALUE;
				break;
			default:
				$this->type = self::TYPE_GROUP;
				if (is_array($value)) {
					foreach ($value as $supports) {
						$this->insert($supports);
					}
				} elseif ($value) {
					$this->process(strtolower($value));
				}
		}
	}


	public function __toString() {
		switch ($this->type) {
			case self::TYPE_GROUP:
				$result = [];
				foreach ($this->childs as $supports) {
					$result[] = '('. $supports .')';
				}
				return ($this->value === 'not' ?  'not ' : '') . implode(' ' . $this->value . ' ', $result);
				break;
			case self::TYPE_VALUE:
				return $this->value ? $this->value[0] . $this->value[1] . ': ' . $this->value[2] : '';
		}
		return '';
	}




	protected function prepare($supports) {
		static $nesting = 0;

		// 限制嵌套层次
		if ($nesting >= self::NESTING) {
			return;
		}

		while (($char = $this->search('{};()')) !== false) {
			switch ($char) {
				case '(':
					// 开始
					$supports->type = self::TYPE_GROUP;

					$buffer = trim($this->buffer);

					if ($supports->childs) {
						// and or 运算符
						if (in_array($buffer, ['and', 'or'], true)) {
							$supports->value = $buffer;
						}
					} elseif (!$supports->parent && $buffer === 'not') {
						// not 运算符
						$supports->value = 'not';
					} else {
						$supports->value = 'and';
					}

					// 清空缓冲区
					$this->buffer = '';

					// 创建对象
					$supports->insert($supports2 = new Supports);

					// 递归
					++$nesting;
					$this->prepare($supports2);
					--$nesting;

					// 无效的对象移除
					if (!$supports2->childs && !$supports2->value) {
						$supports2->parent->delete($supports2);
					}
					break;
				case ')':
					// 结束
					if ($supports->parent && !$supports->childs) {
						$supports->type = self::TYPE_VALUE;
						$array = array_map('trim', explode(':', $this->buffer, 2));
						$privatePrefix = self::privatePrefix($array[0]);
						if (isset(self::$propertys[$array[0]])) {
							$supports->value = [$privatePrefix, $array[0], empty($array[1]) ? '' : preg_replace('/[^0-9a-z !|\/%#.,+-]/i', '', $array[1])];
						}
					}
					$this->buffer = '';
					break 2;
				default:
					// 跳出
					$this->buffer = '';
					break 2;
			}
		}

		// 如果是 not 运算符只允许一个 属性
		if ($supports->value === 'not' && $supports->childs) {
			$supports->childs = [reset($supports->childs)];
		}
	}


	public function insert(Supports $supports, $index = NULL) {
		$supports->parent && $supports->parent->delete($supports);
		$supports->parent = $this;
		if ($index === NULL) {
			$this->childs[] = $supports;
		} else {
			array_splice($this->childs, $index, 0,[$supports]);
		}
		return $supports;
	}




  	public function delete(Supports $supports) {
  		if (($index = array_search($supports, $this->childs, true)) !== false) {
			unset($this->childs[$index]);
			$this->childs = array_values($this->childs);
			$supports->parent = NULL;
		}
		return $supports;
  	}

	public function getIterator() {
		return new ArrayIterator($this->childs);
	}

	public function count() {
		return count($this->childs);
	}
}