<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-05 03:10:27
/*	Updated: UTC 2015-07-21 03:49:46
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use IteratorAggregate, Countable, ArrayIterator;
class Selectors extends Base implements IteratorAggregate, Countable{

	// 嵌套层次限制
	const NESTING = 5;

	// 搜索 和转义的
	const SEARCH = "~>+:#.,[]{}@  \t\n\r\0\x0B";


	/**
	 * $selectors
	 * @var array
	 */
	protected $selectors = [];


	public function __construct($selectors = false) {
		$selectors && $this->process($selectors);
	}

	/**
	 * __toString 对象字符串输出
	 * @return string
	 */
	public function __toString() {
		$selectors = [];
		foreach ($this->selectors as $array) {
			$selector = '';
			foreach ($array as $values) {
				if (is_array($values)) {
					foreach ($values as $value) {
						switch ($value[0]) {
							case '':
							case '.':
							case '#':
								$selector .= $value[0] . $value[1];
								break;
							case '[]':
								foreach($value[1] as &$attributeName) {
									$attributeName = $attributeName;
								}
								unset($attributeName);
								$selector .= '['. implode('|', $value[1]) . ($value[2] ? $value[2] . '"'. addcslashes(str_replace('"', '&quot;', $value[3]), '"\\') .'"' : '') .']';
								break;
							case ':':
								switch ($value[1]) {
									case 'lang':
										$selector .= $value[0] . $value[1] . '('. $value[2]. ')';
										break;
									case 'nth-child':
									case 'nth-last-child':
									case 'nth-of-type':
									case 'nth-last-of-type':
										$value[2][0] = $value[2][0] === '-' ? '-' : (int) $value[2][0];
										$args = $value[2][1] ? $value[2][0] . 'n' . ($value[2][2] < 0 ? intval($value[2][2]) : '+' . intval($value[2][2])) : $value[2][0];
										$selector .= $value[0] . $value[1] . '('. $args. ')';
										break;
									case 'has':
									case 'not':
									case 'matches':
										$selector .= $value[0] . $value[1] . '('. $value[2] . ')';
										break;
									default:
										$selector .= $value[0] . $value[1];
								}
						}
					}
				} else {
					$selector .= $values;
				}
			}
			$selectors[] = $selector;
		}
		return implode(', ', $selectors);
	}


	protected function prepare($selectors) {
		static $stack = [];
		// 限制嵌套层次
		if (count($stack) >= self::NESTING) {
			return;
		}

		$selector = $single = [];
		$char = $this->search(self::SEARCH);
		do {
			if ($char === false) {
				$char = '';
			}

			// 标签名
			if ($this->buffer) {
				$single[] = ['', strtolower($this->buffer)];
				$this->buffer = '';
			}

			switch ($char) {
				case '{':
				case '}':
				case '@':
				case ']':
					// 特殊字符串跳出
					break 2;
				case ')':
					$this->buffer = '';
					if ($stack) {
						break 2;
					}
					break;
				case ',':
					// 逗号
					if ($this->single($single)) {
						$selector[] = $single;
						$single = [];
					} elseif ($selector && !is_array(end($selector))) {
						array_pop($selector);
					}
					if ($selector) {
						$selectors->selectors[] = $selector;
						$selector = [];
					}
					$char = $this->search(self::SEARCH);
					break;
				case '>':
					// 单层 or  多层次 匹配
					if ($this->single($single)) {
						$selector[] = $single;
						$single = [];
					}
					if (isset($this->string{$this->offset}) && $this->string{$this->offset} === '>') {
						++$this->offset;
						if ($selector && !is_array(end($selector))) {
							$selector[] = '>';
						}
						$char = ' ';
					} elseif ($selector) {
						if (!is_array(end($selector))) {
							array_pop($selector);
						}
						$selector[] = '>';
					}
					$char = $this->search(self::SEARCH);
					break;
				case '~':
				case '+':
					// 上一个 or 之上匹配
					if ($this->single($single)) {
						$selector[] = $single;
						$single = [];
					}
					if ($selector) {
						if (!is_array(end($selector))) {
							array_pop($selector);
						}
						$selector[] = $char;
					}
					$char = $this->search(self::SEARCH);
					break;
				case '#':
				case '.':
					// class or id 名
					$key = $char;
					$char = $this->search(self::SEARCH);
					$single[] = [$key, stripcslashes($this->buffer)];
					$this->buffer = '';
					break;
				case '[':
					// [] 类型
					$value = [0 => '[]', 1 => [], 2 => '', 3 => false];
					while (($char = $this->search('~^*|=]')) && $char !== ']') {

						// 不是等号 但是下一个是等号  并且 当前不是* 或属性不是空
						if (($char !== '=' && isset($this->string{$this->offset}) && $this->string{$this->offset} === '=') && ($char !== '*' || $this->buffer)) {
							$char .= '=';
							++$this->offset;
						}

						switch ($char) {
							case '=':
							case '~=':
							case '*=':
							case '^=':
							case '|=':
								// 到运算符了
								if ($this->buffer || !$value[1]) {
									$value[1][] = stripcslashes($this->buffer);
								}
								$value[2] = $char;
								$value[3] = '';
								$this->buffer = '';
								break 2;
							case '|':
								// 分段
								if ($this->buffer) {
									$value[1][] = stripcslashes($this->buffer);
								}
								$this->buffer = '';
								break;
							default:
								// 其他就链接上
								$this->buffer .= $char;
						}
					}

					// 不是  ] 跳到 ]
					if ($char && $char !== ']') {
						$this->search(']');
					}
					// 如果有运算符这个就是 值
					if ($value[2]) {
						if (strlen($this->buffer) > 1 && (($quote = substr($this->buffer, 0, 1)) === '"' || $quote === '\'')) {
							$value[3] = stripcslashes(substr($this->buffer, 1, -1));
						} else {
							$value[3] = stripcslashes($this->buffer);
						}
					} else {
						$value[1][] = stripcslashes($this->buffer);
					}
					if ($value[1] = array_filter($value[1])) {
						$single[] = $value;
					}
					$this->buffer = '';
					// 跳到下一个位置
					$char = $this->search(self::SEARCH);
					break;
				case ':':
					if (isset($this->string{$this->offset}) && $this->string{$this->offset} === ':') {
						++$this->offset;
					}
					$char = $this->search(self::SEARCH);

					$buffer = explode('(', $this->buffer, 2);
					if (!empty($buffer[1]) && substr($buffer[1], -1, 1) === ')') {
						$buffer[1] = substr($buffer[1], 0, -1);
					}
					self::privatePrefix($buffer[0]);
					$this->buffer = '';
					switch ($buffer[0]) {
						case 'first-child':
						case 'last-child':
						case 'only-child':
						case 'first-of-type':
						case 'last-of-type':
						case 'only-of-type':
						case 'read-only':
						case 'read-write':
						case 'disabled':
						case 'enabled':
						case 'required':
						case 'optional':
						case 'empty':
						case 'root':
						case 'target':
						case 'in-range':
						case 'out-of-range':
						case 'active':
						case 'checked':
						case 'focus':
						case 'hover':
						case 'link':
						case 'visited':
						case 'valid':
						case 'invalid':
							$single[] = [':', $buffer[0], false];
							break;
						case 'after':
						case 'before':
						case 'first-letter':
						case 'first-line':
						case 'selection':
							$single[] = [':', ':'. $buffer[0], false];
							break;
						case 'nth-child':
						case 'nth-last-child':
						case 'nth-of-type':
						case 'nth-last-of-type':
							if (!empty($buffer[1]) && preg_match('/^\s*(even|odd|[0-9+\-]+(?:n[0-9+\-]+)?)\s*$/i', $buffer[1], $matches)) {
								$args = strtolower($matches[1]);
								if ($args === 'even') {
									$args = [2, 'n', 0];
								} elseif ($args === 'odd') {
									$args = [2, 'n', 1];
								} elseif (is_numeric($args)) {
									$args = [intval($args), '', 0];
								} else {
									$args = explode('n', $args, 2) + [1 => 0];
									$args = [$args[0] === '-' ? '-': intval($args[0]), 'n', intval($args[1])];
								}
								$single[] = [':', $buffer[0], $args];
							}
							break;
						case 'lang':
							if (!empty($buffer[1]) && preg_match('/^\s*([a-z\-]{2,10})\s*$/i', $buffer[1], $matches)) {
								$single[] = [':', $buffer[0], $matches[1]];
							}
							break;
						case 'has':
						case 'not':
						case 'matches':
							// 没匹配到 开始括号
							if (!empty($buffer[1])) {
								$selectors2 = new Selectors;
								$stack[] = $buffer[0];
								$this->process($buffer[1], $selectors2);
								array_pop($stack);
								if ((!$stack || (reset($stack) !== 'matches' && $buffer[0] !== 'matches')) && $selectors2->count()) {
									$single[] = [':', $buffer[0], $selectors2];
								}
							}
							break;
					}
					break;
				default:
					if ($this->single($single)) {
						$selector[] = $single;
						$single = [];
					}
					if ($selector && is_array(end($selector))) {
						$selector[] = ' ';
					}
					$char = $this->search(self::SEARCH);
			}
		} while ($char !== false || $this->buffer);
		if ($this->single($single)) {
			$selector[] = $single;
			$single = [];
		} elseif ($selector && !is_array(end($selector))) {
			array_pop($selector);
		}
		if ($selector) {
			$selectors->selectors[] = $selector;
			$selector = [];
		}
	}
	/**
	 * count 选择器数量
	 * @return
	 */
	public function count() {
		return count($this->selectors);
	}

	public function getIterator() {
		return new ArrayIterator($this->selectors);
	}

	public function toArray() {
		return $this->selectors;
	}


	protected function single(array &$single) {
		if (!$single) {
			return false;
		}
		foreach ($single as $key => $value) {
			switch ($value[0]) {
				case '':
					$continue = preg_match('/^(\*|[a-z_-][a-z0-9_-]*)$/i', $value[1]);
					break;
				case '#':
				case '.':
					$continue = preg_match('/^[a-z_-][a-z0-9_-]*$/i', $value[1]);
					break;
				case '[]':
					$continue = false;
					foreach ($value[1] as $attributeName) {
						if (!$continue = preg_match('/^(\*|[a-z_-][a-z0-9_-]*)$/i', $attributeName)) {
							break;
						}
					}
					break;
				default:
					$continue = true;
					break;
			}
			// 不合法的属性
			if (!$continue) {
				unset($single[$key]);
				continue;
			}
			// 标签 和 id 不能多个 多个就删除
			if (in_array($value[0], ['', '#'], true)) {
				if (isset($keys[$value[0]])) {
					unset($single[$key]);
					continue;
				}
				$keys[$value[0]] = true;
			}
		}

		// 规则排序
		usort($single, [$this, 'sort']);
		return !empty($single);
	}

	protected function sort($a, $b) {
		static $sort = [
			'' => 0,		// 单个
			'#' => 1,		// 单个
			'.' => 2,		// 多个
			'[]' => 3,		// 多个
			':' => 4,		// 多个
		];
		return $sort[$a[0]] ===  $sort[$b[0]] ? 0 : ($sort[$a[0]] > $sort[$b[0]] ? 1 : -1);
	}
}
