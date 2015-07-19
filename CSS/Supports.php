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
/*	Updated: UTC 2015-07-13 09:24:58
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use IteratorAggregate, ArrayIterator, Countable;

class Supports extends Base implements IteratorAggregate, Countable{

	const NESTING = 10;

	protected $value;

	protected $parent;

	protected $childs = [];

	public function __construct($value = false) {
		$this->value = trim($value);
	}


	public function __toString() {
		if ($this->childs) {
			$result = [];
			foreach ($this->childs as $supports) {
				$result[] = '('. $supports .')';
			}
			return (strcasecmp($this->value, 'not') === 0  ? 'not ' : '') . implode(' ' . (strcasecmp($this->value, 'and') === 0 ? 'and' : 'or') . ' ', $result);
		}
		return (string) $this->value;
	}




	protected function prepare($supports) {
		static $nesting = 0;

		// 限制嵌套层次
		if ($nesting >= self::NESTING) {
			return;
		}

		while (($char = $this->search('{};()')) !== false) {
			if ($char === '(') {
				// 属性的
				if ($supports->parent && !$supports->childs && strpos($this->buffer, ':') !== false) {
					$brackets = 1;
				    $this->buffer .= '(';
					while ($brackets > 0 && ($char = $this->search('()'))) {
						if ($char === '(') {
							++$brackets;
							$this->buffer .= '(';
						} elseif ($brackets > 0) {
							--$brackets;
							$this->buffer .= ')';
						}
					}
					if ($brackets > 0) {
						$this->buffer .= str_repeat(')', $brackets);
					}
					continue;
				}


				// 开始
				if ($supports->childs) {
					// and or 运算符
					if (in_array($buffer = strtolower(trim($this->buffer)), ['and', 'or'], true)) {
						$supports->value = $buffer;
					}
				} elseif (!$supports->parent && strcasecmp(trim($this->buffer), 'not') === 0) {
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
				continue;
			}


			// 结束
			if ($char === ')') {
				if ($supports->parent && !$supports->childs) {
					$array = array_map('trim', explode(':', $this->buffer, 2)) + [1 => ''];
					$array[0] = strtolower($array[0]);
					if (self::name($array[0]) && self::blacklistName($array[0])) {
						$supports->value = implode(':', $array);
					}
				}
			}
			$this->buffer = '';
			break;
		}


		// 如果是 not 运算符只允许一个 属性
		if ($supports->value === 'not' && $supports->childs) {
			$supports->childs = [reset($supports->childs)];
		}
	}


			/*if ($char === '(') {
				// 属性的
				if ($supports->parent && !$supports->childs && strpos($this->buffer, ':') !== false) {
					$brackets = 1;
				    $this->buffer .= '(';
					while ($brackets > 0 && ($char = $this->search('()'))) {
						if ($char === '(') {
							++$brackets;
							$this->buffer .= '(';
						} elseif ($brackets > 0) {
							--$brackets;
							$this->buffer .= ')';
						}
					}
					if ($brackets > 0) {
						$this->buffer .= str_repeat(')', $brackets);
					}
					continue;
				} else {
					// 开始
					$supports->type = self::TYPE_GROUP;
					if ($supports->childs) {
						// and or 运算符
						if (in_array($buffer = strtolower(trim($this->buffer)), ['and', 'or'], true)) {
							$supports->value = $buffer;
						}
					} elseif (!$supports->parent && strcasecmp(trim($this->buffer), 'not') === 0) {
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
				}
				continue;
			}






			if ($char === ')') {
				// 结束
				if ($supports->parent && !$supports->childs) {
					$supports->type = self::TYPE_VALUE;
					$supports->value = self::TYPE_VALUE;
					$array = array_map('trim', explode(':', $this->buffer, 2));
				}
				$this->buffer = '';
			}
			break;








			/*
			switch ($char) {
				case '(':
					// 开始
					$supports->type = self::TYPE_GROUP;

					if ($supports->parent && !$supports->childs && strpos($this->buffer, ':') !== false) {
						// 属性的
						$brackets = 1;
					    $this->buffer .= '(';
						while ($brackets > 0 && ($char = $this->search('()'))) {
							if ($char === '(') {
								++$brackets;
								$this->buffer .= '(';
							} elseif ($brackets > 0) {
								--$brackets;
								$this->buffer .= ')';
							}
						}
						if ($brackets > 0) {
							$this->buffer .= str_repeat(')', $brackets);
						}
						break;
					} elseif ($supports->childs) {
						// and or 运算符
						if (in_array($buffer = strtolower(trim($this->buffer)), ['and', 'or'], true)) {
							$supports->value = $buffer;
						}
					} elseif (!$supports->parent && strcasecmp(trim($this->buffer), 'not') === 0) {
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
						$supports->value = self::TYPE_VALUE;
						$array = array_map('trim', explode(':', $this->buffer, 2));
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
	}*/


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