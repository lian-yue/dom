<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-10 15:05:52
/*	Updated: UTC 2015-07-21 15:34:53
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use IteratorAggregate, ArrayIterator, Countable;
class MediaCondition extends Base implements IteratorAggregate, Countable{

	const TYPE_ROOT = 1;

	const TYPE_GROUP = 2;

	const TYPE_FEATURE_PLAIN = 3;

	const TYPE_FEATURE_BOOLEAN = 4;

	const TYPE_FEATURE_RANGE = 5;

	const NESTING = 10;

	protected $value = [];

	protected $type;

	protected $childs = [];

	protected $parent;

	// 允许所有的 name
	protected static $names = [
		'width',
		'height',
		'aspect-ratio',
		'orientation',
		'resolution',
		'scan',
		'grid',
		'update-frequency',
		'overflow-block',
		'overflow-inline',
		'color',
		'color-index',
		'monochrome',
		'inverted-colors',
		'pointer',
		'hover',
		'any-pointer',
		'any-hover',
		'light-level',
		'scripting',
		'device-width',
		'device-height',
		'device-aspect-ratio',

		'images-in-menus',
		'mac-graphite-theme',
		'maemo-classic',
		'device-pixel-ratio',
		'os-version',
		'scrollbar-end-backward',
		'scrollbar-end-forward',
		'scrollbar-start-backward',
		'scrollbar-start-forward',
		'scrollbar-thumb-proportional',
		'touch-enabled',
		'windows-classic',
		'windows-compositor',
		'windows-default-theme',
		'windows-glass',
		'windows-theme',
	];

	// 类型
	protected static $types = [
		'all',
		'print',
		'screen',
		'speech',
		'tty',
		'tv',
		'projection',
		'handheld',
		'braille',
		'embossed',
		'aural'
	];

	public function __construct($value = false, $type = self::TYPE_ROOT) {
		switch ($type) {
			case self::TYPE_FEATURE_PLAIN:
			case self::TYPE_FEATURE_BOOLEAN:
			case self::TYPE_FEATURE_RANGE:
				if (is_array($value)) {
					$this->value = $value;
				}
				break;
			default:
				$this->type = $type === self::TYPE_GROUP ? self::TYPE_GROUP : self::TYPE_ROOT;
				if (is_array($value)) {
					foreach($value as $media) {
						$this->insert($media);
					}
				} elseif ($value) {
					$this->process(preg_replace('/([()])/', ' $1 ', $value));
				}
		}
	}



	public function __toString() {
		switch ($this->type) {
			case self::TYPE_FEATURE_PLAIN:
				return $this->value ? $this->value[0] . ': ' . $this->value[1] : '';
				break;
			case self::TYPE_FEATURE_BOOLEAN:
				return $this->value[0];
				break;
			case self::TYPE_FEATURE_RANGE:
				$result = [];
				foreach ($this->value as $value) {
					$result[] = $value;
				}
				return implode(' ', $result);
				break;
			case self::TYPE_GROUP:
				$result = [];
				foreach ($this->childs as $media) {
					$result[] = '('. $media .')';
				}
				return ($this->value[0] === 'not' ? 'not ' : '') . implode(' '. $this->value[0] . ' ', $result);
				break;
			case self::TYPE_ROOT:
				$result = [];
				foreach ($this->childs as $media) {
					$result[] = '('. $media .')';
				}
				$result = trim(($this->value[1] ? $this->value[1] . ' ' : '') . ($this->value[2] ? $this->value[2] . ' ' : '') . ($this->value[0] === 'not' ? 'not ' : '') . implode(' ' . $this->value[0] . ' ', $result));
				if (!$result) {
					$result = 'all';
				}
				return $result;
		}
		return '';
	}



	public function insert(MediaCondition $media, $index = NULL) {
		$media->parent && $media->parent->delete($media);
		$media->parent = $this;
		if ($index === NULL) {
			$this->childs[] = $media;
		} else {
			array_splice($this->childs, $index, 0,[$media]);
		}
		return $media;
	}




  	public function delete(MediaCondition $media) {
  		if (($index = array_search($media, $this->childs, true)) !== false) {
			unset($this->childs[$index]);
			$this->childs = array_values($this->childs);
			$media->parent = NULL;
		}
		return $media;
  	}


	protected function prepare($media) {
		static $nesting = 0;
		// 限制嵌套层次
		if ($nesting >= self::NESTING) {
			return;
		}
		$conditions = [];
		while (!in_array($char = $this->search(" \t\n\r\0\x0B,;{}"), [',', ';', '{', '}', false], true)) {
			$conditions[] = $this->buffer;
			$this->buffer = '';
		}
		$conditions[] = $this->buffer;

		$conditions = array_filter(array_map('trim', $conditions));

		$media->value = [false, false, false];


		if ($media->type === self::TYPE_ROOT) {
			$key = 0;

			// 设备运算符 only not
			if (isset($conditions[$key]) && in_array(strtolower($conditions[$key]), ['only', 'not'], true)) {
				$media->value[1] = strtolower($conditions[$key]);
				unset($conditions[$key]);
				++$key;
			}

			// 设备类型
			if (isset($conditions[$key]) && in_array(strtolower($conditions[$key]), self::$types, true)) {
				$media->value[2] = strtolower($conditions[$key]);
				unset($conditions[$key]);
				++$key;
			} else {
				$media->value[1] = false;
			}
		}



		// 遍历
		foreach ($conditions as $condition) {
			if ($condition{0} === '(') {
				$condition = substr($condition, 1, -1);
				$offset = strcspn($condition, ':<>(');
				$length = strlen($condition);
				$media->insert($media2 = new MediaCondition);

				if ($offset < $length && $condition{$offset} === '(') {
					// 创建对象
					$media2->type = self::TYPE_GROUP;

					++$nesting;
					$this->process($condition, $media2);
					--$nesting;
				} else {
					if ($offset >= $length) {
						// boolean 属性
						$media2->type = self::TYPE_FEATURE_BOOLEAN;
						if ($name = $this->_name($condition)) {
							$media2->value = [$name];
						}
					} elseif ($condition{$offset} === ':') {
						// plain 属性
						$media2->type = self::TYPE_FEATURE_PLAIN;
						$name = $offset ? substr($condition, 0, $offset) : '';
						$value = trim(substr($condition, $offset + 1));
						if (($name = $this->_name($name)) && ($value = $this->_value($value))) {
							$media2->value = [$name, $value];
						}
					} else {
						// 范围属性
						$media2->type = self::TYPE_FEATURE_RANGE;

						// 第一个属性
						$media2->value = [$offset ? substr($condition, 0, $offset) : ''];

						// 第一个判断
						$media2->value[1] = $condition{$offset};
						++$offset;
						if (isset($condition{$offset}) && $condition{$offset} === '=') {
							$media2->value[1] .= '=';
							++$offset;
						}

						// 截断缓冲区
						$condition = substr($condition, $offset);
						$offset = strcspn($condition, '<>');

						// 第二个属性
						$media2->value[2] = $offset ? substr($condition, 0, $offset) : false;

						// 第二个判断
						if ($offset < strlen($condition)) {
							$media2->value[3] = $condition{$offset};
							++$offset;
							if (isset($condition{$offset}) && $condition{$offset} === '=') {
								$media2->value[3] .= '=';
								++$offset;
							}
							// 第三个属性
							$media2->value[4] = substr($condition, $offset);
						}



						// 过滤头尾空格
						$media2->value = array_map('trim', $media2->value);


						if (isset($media2->value[4])) {
							// 2个值的
							if (($name = $this->_name($media2->value[2])) && $this->_value($media2->value[0]) && $this->_value($media2->value[4])) {
								$media2->value[2] = $name;
							} else {
								$media2->value = [];
							}
						} else {
							// 1个值的
							if ($name = $this->_name($media2->value[0])) {
								if ($this->_value($media2->value[2])) {
									$media2->value[0] = $name;
								} else {
									$media2->value = [];
								}
							} else {
								if (($name = $this->_name($media2->value[2])) && $this->_value($media2->value[0])) {
									$media2->value[2] = $name;
								} else {
									$media2->value = [];
								}
							}
						}
					}
				}
				// 无效的对象移除
				if ($media2->parent && !$media2->childs && !$media2->value) {
					$media2->parent->delete($media2);
				}
			} elseif (!$media->value[0] && in_array(strtolower($condition), ['and', 'not', 'or'], true)) {
				$media->value[0] = strtolower($condition);
			}
		}
		if (!$media->value[0]) {
			$media->value[0] = 'and';
		}


		// 如果是 not 运算符只允许一个 属性
		if ($media->childs && ($media->type === self::TYPE_ROOT || $media->type === self::TYPE_GROUP) && $media->value[0] === 'not') {
			$media->childs = [reset($media->childs)];
		}
	}


	private function _name($name) {
		if (!$name = trim($name)) {
			return false;
		}

		// 变量
		if (self::name($name, true)) {
			return $name;
		}
		$name = strtolower($name);
		if (in_array(substr($name, 0, 4), ['max-', 'min-'], true)) {
			$name2 = substr($name, 4);
			self::privatePrefix($name2);
		} else {
			$name2 = $name;
			self::privatePrefix($name2);
			if (in_array(substr($name2, 0, 4), ['max-', 'min-'], true)) {
				$name2 = substr($name2, 4);
			}
		}
		return in_array($name2, self::$names, true) ? $name : false;
	}

	private function _value($value) {
		return $value && preg_match('/^[0-9a-z \%\/.\-\+]+$/', $value);
	}

	public function getIterator() {
		return new ArrayIterator($this->childs);
	}

	public function count() {
		return count($this->childs);
	}
}