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
/*	Updated: UTC 2015-07-07 03:09:37
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




	abstract protected function prepare($obj);



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

	protected static function isVar($name) {
		return $name && preg_match('/^\-\-[a-z][0-9a-z_-]*$/i', $name);
	}




	/**
	 * url url地址匹配
	 * @param  string     $url url地址
	 * @return string|boolean
	 */
	protected static function url($url) {
		if (!$url) {
			return false;
		}
		if (!$url = preg_replace('/(["\'()#*;<>\\\\]|\s)/', '', $url)) {
			return false;
		}
		$scheme = parse_url($url, PHP_URL_SCHEME);
		if ($scheme && strcasecmp($scheme, 'http') !== 0 && strcasecmp($scheme, 'https') !== 0) {
			return false;
		}
		if (strpos($url, ':') !== false && !$scheme) {
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
		$search .= '/\\\'"';
		while (($char = $this->_search($search)) !== false) {
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
					$quote = $char;
					$this->buffer .= $quote;
					while (($char = $this->_search('\\' . $quote)) === '\\') {
						$this->buffer .= '\\';
						if (isset($this->string{$this->offset})) {
							$this->buffer .= $this->string{$this->offset};
						}
						++$this->offset;
					}
					if ($char !== false) {
						$this->buffer .= $char;
					}
					break;
				default:
					return $char;
			}
		}
		return false;
	}

	private function _search($search) {
		$offset = $this->offset + strcspn($this->string, $search, $this->offset);
		if ($offset < $this->length) {
			$this->buffer .= substr($this->string, $this->offset, $offset - $this->offset);
			$this->offset = $offset + 1;
			return $this->string{$offset};
		}
		$this->buffer .= substr($this->string, $this->offset);
		$this->offset = $this->length;
		return false;
	}
}