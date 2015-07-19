<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-19 10:02:30
/*	Updated: UTC 2015-07-19 13:59:58
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
abstract class Base{

	protected function process($value) {
		$this->string = trim($value);
		$this->length = strlen($this->string);
		$this->offset = 0;
		$this->buffer = '';
		$this->prepare($this);
		unset($this->string, $this->length, $this->offset, $this->buffer);
	}

	/**
	 * ascii 判断 ascii 代码 范围
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	protected static function ascii($value) {
		return preg_match("/^[\r\n\t -~]*$/", $value);
	}

	protected static function name($name, $var = NULL) {
		$name = trim($name);
		if (self::expression($name)) {
			return false;
		}
		if ($var !== NULL && (((bool)$var) !== (substr($name, 0, 2) === '--'))) {
			return false;
		}
		if (!preg_match('/^[_-]*[a-z][0-9a-z_-]*$/i', $name)) {
			return false;
		}
		return $name;
	}

	protected static function expression($value) {
		return stripos($value, 'expression') !== false || stripos($value, 'script') !== false || stripos($value, 'eval') !== false;
	}

	protected static function value($value) {
		$value = trim($value, " \t\n\r\0\x0B;");
		if (strpos($value, '&#') !== false) {
			return false;
		}
		if (!$value) {
			return $value;
		}

		$value = preg_replace('/\s+/', ' ', $value);
		$offset = 0;
		$length = strlen($value);
		$brackets = 0;
		$url = false;
		while (($offset2 = $offset + ($length2 = strcspn($value, '\\"\'()', $offset))) < $length) {
			$string = substr($value, $offset, $length2);
			if (self::expression($string) || !self::ascii($string)) {
				return false;
			}
			switch ($value{$offset2}) {
				case '(':
					if ($brackets > 5 || $url) {
						return false;
					}
					if (strcasecmp(substr($string, -3, 3), 'url') === 0) {
						$url = $offset2 + 1;
					}
					++$brackets;
					break;
				case ')':
					if ($brackets <= 0) {
						return false;
					}

					if ($url) {
						if (!$url = substr($value, $url, $offset2 - $url)) {
							return false;
						}
						if (in_array($url{0}, ['"', '\''], true)) {
							$url = substr($url, 1, -1);
						}
						if (!self::url($url)) {
							return false;
						}
						$url = false;
					}
					--$brackets;
					break;
				case '"':
				case '\'':
					$search = $value{$offset2};
					++$offset2;
					$offset3 = $offset2;

					// 引号 跳到下一个 字符串去
					while ((($offset4 = $offset3 + strcspn($value, '\\' . $search, $offset3)) < $length) && $value{$offset4} === '\\') {
						++$offset4;
						if (!isset($value{$offset4})) {
							return false;
						}
						++$offset4;
					}

					// 只有一个引号的 返回 false
					if (!isset($value{$offset4})) {
						return false;
					}
					$offset2 = $offset4;
					break;
				default:
					return false;
			}

			// 设置新的开始
			$offset = $offset2 + 1;
		}

		// 没闭合的
		if ($brackets) {
			return false;
		}

		return $value;
	}



	/**
	 * privatePrefix 私有浏览器前缀
	 * @param  string  &$name
	 * @param  boolean $hack ie 的
	 * @return string  前缀
	 */
	// https://en.wikipedia.org/wiki/CSS_filter
	protected static function privatePrefix(&$name, $hack = false) {
		$name = strtolower(trim($name));
		if (!$name) {
			return '';
		}

		// hask
		if ($hack && in_array($name{0}, ['*', '_', '+'], true)) {
			$prefix = $name{0};
			$name = substr($name, 1);
			return $prefix;
		}

		// 私有前缀
		if (!preg_match('/^(\-ah\-|\-apple\-|\-atsc\-|\-epub\-|\-hp\-|\-khtml\-|\-moz\-|\-ms\-|mso\-|\-o\-|prince\-|\-rim\-|\-ro\-|\-tc\-|\-wap\-|\-webkit\-|\-xv\-)/', $name, $matches)) {
			return '';
		}
		$name = substr($name, strlen($matches[1]));
		return $matches[1];
	}

	/**
	 * url url地址匹配
	 * @param  string     $url url地址
	 * @return string|boolean
	 */
	protected static function url($url) {
		if (!$url || !self::ascii($url) || preg_match('/(["\'()]|\s)/', $url)) {
			return false;
		}

		// image 数据
		if (preg_match('/^data\:image\/[a-z]+;\s*base64/i', $url)) {
			return true;
		}

		if (!$parse = parse_url($url)) {
			return false;
		}

		if (isset($parse['scheme']) && strcasecmp($parse['scheme'], 'http') !== 0 && strcasecmp($parse['scheme'], 'https') !== 0) {
			return false;
		}
		return $url;
	}




	/**
	 * search 解析搜索字符串
	 * @param  string   	    $search    搜索的字符串
	 * @param  Rule|boolean     $rule      如果是对象 储存进对象 如果 是 true 储存进缓冲区 否则 销毁并且跳过
	 * @return string|boolean
	 */
	protected function search($search, $rule = false) {
		$search .= '/\\\'"()';

		$brackets = 0;
		while (($offset = $this->offset + ($length = strcspn($this->string, $search, $this->offset))) < $this->length) {
			$char = $this->string{$offset};

			$this->buffer .= substr($this->string, $this->offset, $length);
			$this->offset = $offset + 1;

			switch ($char) {
				case '/':
					if (isset($this->string{$this->offset}) && $this->string{$this->offset} === '*') {
						//  注释
						++$this->offset;
						$offset = strpos($this->string, '*/', $this->offset);
						if ($offset === false) {
							$offset = $this->length;
						}
						if ($rule instanceof Rule) {
							$rule->insertRule(new Rule(substr($this->string, $this->offset, $offset - $this->offset), Rule::COMMENT_RULE));
						} elseif ($rule) {
							$this->buffer .= '/*'. substr($this->string, $this->offset, $offset - $this->offset) . '*/';
						}
						$this->offset = $offset + 2;
					} else {
						// 其他储存
						$this->buffer .= '/';
					}
					break;
				case '\\':
					// 跳到下一个
					$this->buffer .= '\\' . (isset($this->string{$this->offset}) ? $this->string{$this->offset} : '');
					++$this->offset;
					break;
				case '"':
				case '\'':
					// 引号 跳到下一个 字符串去
					$this->buffer .= $char;
					while ((($offset = $this->offset + ($length = strcspn($this->string, $char . '\\', $this->offset))) < $this->length) && $this->string{$offset} === '\\') {
						$this->buffer .= substr($this->string, $this->offset, $length);
						++$offset;
						if (isset($this->string{$offset})) {
							$this->buffer .= $this->string{$offset};
						}
						$this->offset = $offset + 1;
					}
					$this->buffer .= substr($this->string, $this->offset, $length);
					if ($offset < $this->length) {
						$this->buffer .= $char;
						$this->offset = $offset + 1;
					} else {
						$this->offset = $offset;
					}
					//die;
					break;
				case '(':
					$this->buffer .= '(';
					++$brackets;
					break;
				case ')':
					$this->buffer .= ')';
					if ($brackets > 0) {
						--$brackets;
					}
					break;
				default:
					if ($brackets <= 0) {
						return $char;
					}
			}
		}

		$this->buffer .= substr($this->string, $this->offset, $length);
		$this->offset = $this->length;
		return false;
	}
}