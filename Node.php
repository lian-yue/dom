<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-05-23 11:20:19
/*	Updated: UTC 2015-07-22 05:31:44
/*
/* ************************************************************************** */
namespace Loli\DOM;
use ArrayAccess, IteratorAggregate, ArrayIterator, JsonSerializable, Countable, Loli\DOM\CSS\Selectors;
class Node implements ArrayAccess, IteratorAggregate, JsonSerializable, Countable{

	// 元素节点
	const ELEMENT_NODE = 1;


	// 属性节点 [其他对象实现了]
	//const ATTRIBUTE_NODE = 2;

	// 文字节点
	const TEXT_NODE = 3;

	// CDATASection 节点  <![CDATA[内容]]>
	const CDATA_SECTION_NODE = 4;


	// < ? 开头的< ?xml
	//const PROCESSING_INSTRUCTION_NODE = 7;


	//  注释节点
	const COMMENT_NODE = 8;


	// 根文档
	const DOCUMENT_NODE = 9;

	// 空格字符串
	const BLANK = " \t\n\r\0\x0B";

	// 跳到标签名
	const TAG_NAME = " \t\n\r\0\x0B/>";

	// 跳到标签结束
	const TAG_END = " \t\n\r\0\x0B>";


	// 最大嵌套层次
	const NESTING = 50;

	// 节点节点
	public $nodeType = 1;


	// 节点值
	public $nodeValue;


	// 节点属性
	public $attributes;


	// 父节点
	public $parentNode;



	// 子节点
	public $childNodes = [];


	// 元素节点标签名 和js不同 是小写
	public $tagName;

	// 格式化
	protected $format = false;


	// 所有 单标签 无结束标签的
	protected static $singleTags = [
		'base' => true,
		'basefont' => true,
		'br' => true,
		'col' => true,
		'embed' => true,
		'frame' => true,
		'hr' => true,
		'img' => true,
		'input' => true,
		'keygen' => true,
		'link' => true,
		'meta' => true,
		'param' => true,
		'source' => true,
		'track' => true,
		'spacer' => true,
	];





	// 字符串标签
	protected static $textTags = [
		'style' => true,
		'script' => true,
		'textarea' => true,
	];


	// node hash id
	public $hash;

	public function __construct($value = NULL, $nodeType = 0) {
		$this->hash = spl_object_hash($this);
		switch ($nodeType) {
			case self::ELEMENT_NODE:
				$this->tagName = strtolower($value);
				$this->nodeType = self::ELEMENT_NODE;
				$this->attributes = new Attributes;
				break;
			case self::TEXT_NODE:
			case self::CDATA_SECTION_NODE:
			case self::COMMENT_NODE:
				$this->nodeType = $nodeType;
				$this->nodeValue = (string) $value;
				break;
			default:
				$this->nodeType = self::DOCUMENT_NODE;
				$this->string = trim(mb_convert_encoding((string) $value,'utf-8', 'auto'));
				$this->length = strlen($this->string);
				$this->offset = 0;
				$this->buffer = '';
				$this->prepare($this);
				$this->compact();
				unset($this->string, $this->length, $this->offset, $this->buffer);
		}
	}


	//  预处理
	protected function prepare(Node $node) {
		static $nesting = 0;
		// 限制嵌套层次
		if ($nesting >= self::NESTING) {
			return;
		}

		while ($this->pos('<')) {
			if (!isset($this->string{$this->offset})) {
				$this->buffer .= '<';
				continue;
			}

			$char = $this->string{$this->offset};
			switch ($char) {
				case '/':
					// </ 开头的结束标签
					// 结束标签

					++$this->offset;

					// 如果是当前是标签
					if ($node->tagName) {
						// 储存前面的字符串
						$buffer = $this->buffer;
						$this->buffer = '';

						// 跳到空格或 > 处
						$char = $this->cspn(self::TAG_NAME);

						// 如果不是 > 再跳下 到 >处 并且跳动不储存
						if ($char !== '>') {
							$this->pos('>', false);
						}

						// 如果标签相同结束匹配
						if (strcasecmp($this->buffer, $node->tagName) === 0) {
							// 储存字符串
							if ($this->buffer !== '') {
								$node->appendChild(self::createTextNode($buffer));
								$this->buffer = '';
							}
							break 2;
						}

						// 标签不相同还原
						$this->buffer = $buffer;
					} else {
						//  当前不是标签跳到 > 处
						$this->pos('>', false);
					}
					break;
				case '?':
					// <?xml 等 <?开头的
					break;
				case '!':
					// <! 开头的

					// 储存字符串
					if ($this->buffer !== '') {
						$node->appendChild(self::createTextNode($this->buffer));
						$this->buffer = '';
					}

					if (substr($this->string, $this->offset, 8) === '![CDATA[') {
						// CDATA  文本
						$this->offset += 8;
						$this->pos(']]>');
						$node->appendChild(self::createCDATASection($this->buffer));
					} else {
						// 注释
						if (substr($this->string, $this->offset, 3) == '!--') {
							$this->offset += 3;
							$this->pos('-->');
						} else {
							++$this->offset;
							$this->pos('>');
						}
						$comment = $this->buffer;
						$node->appendChild(self::createComment($comment));
					}

					// 清空缓冲区
					$this->buffer = '';
					break;
				default:
					// 其他
					// 储存字符串
					if ($this->buffer !== '') {
						$node->appendChild(self::createTextNode($this->buffer));
						$this->buffer = '';
					}

					// 是标签
					if ($char === '_' || ($char >= 'a' && $char <= 'z') || ($char >= 'A' && $char <= 'Z')) {
						$char = $this->cspn(self::TAG_NAME);
						$tagName = strtolower($this->buffer);
						$this->buffer = '';

						// 创建标签
						$node->appendChild($element = self::createElement($tagName));

						// 有属性的除属性的
						while ($char !== '>' && $char !== false) {

							// 跳值去
							$char = $this->cspn('=>');
							switch ($char) {
								case '=':
									// 带有值的

									// 名字
									$name = $this->buffer;
									$this->buffer = '';
									// 遍历
									do {
										// 偏移不存在
										if (!isset($this->string{$this->offset})) {
											break;
										}

										// 读取一个字节
										$char = $this->string{$this->offset};

										// 加偏移
										++$this->offset;


										switch ($char) {
											case '>':
												// 如果是 > 结束
												break 2;
											case '"':
											case '\'':
												// 如果是 引号读到下一个引号去
												$this->pos($char);
												break 2;
											default:
												// 无引号的
												if (trim($char, self::BLANK)) {
													$this->buffer = $char;
													$char = $this->cspn(self::TAG_END);
													break 2;
												}
												break;
										}
									} while (true);

									// 设置属性
									$element->setAttribute($name, $this->buffer);
									$this->buffer = '';
									break;
								default:
									// 只有参数没有值的
									strpos($this->buffer, '/') === false && $element->setAttribute($this->buffer, true);
							}
							$this->buffer = '';
						}


						if (isset(self::$singleTags[$tagName])) {
							// 单标签
						} elseif (isset(self::$textTags[$tagName])) {
							// 字符串标签里面不允许嵌套任何标签的

							// 跳到结束标签
							$this->pos('</' . $tagName, true, true);

							// 插入文本
							$element->appendChild(self::createTextNode($this->buffer));

							// 跳到 > 去
							$this->pos('>', false);

							// 清空缓冲区
							$this->buffer = '';
						} elseif (preg_match('/^[a-z_-][a-z0-9_:-]*$/i', $tagName)) {
							// 其他标签递归
							++$nesting;
							$this->prepare($element);
							--$nesting;
						}
					}
			}

		}

		if ($this->buffer !== '') {
			$node->appendChild(self::createTextNode($this->buffer));
			$this->buffer = '';
		}
	}



	public function __destruct() {
		unset($this->parentNode);
		unset($this->childNodes);
		unset($this->attributes);
	}



	public function __toString() {
		switch ($this->nodeType) {
			// 元素节点
			case self::ELEMENT_NODE:
				if (isset(self::$singleTags[$this->tagName])) {
					$return = '<' . $this->tagName . $this->attributes . ' />';
				} else {
					if ($this->firstChild && $this->firstChild->nodeType === self::ELEMENT_NODE && $this->lastChild && $this->lastChild->nodeType === self::ELEMENT_NODE) {
						$t2 =  "\n" . str_repeat("\t", $this->format);
						$t1 =  $t2 . "\t";
					} else {
						$t2 = $t1 = '';
					}
					$return = '<' . $this->tagName . $this->attributes . '>' . $t1. $this->innerHTML . $t2 . '</'.$this->tagName.'>';
				}
				if ($this->format !== false && ($nextSibling = $this->nextSibling) && $nextSibling->nodeType === self::ELEMENT_NODE) {
					$return .= "\n" . str_repeat("\t", $this->format);
				}
				break;


			// 字符串节点
			case self::TEXT_NODE:
				if ($this->parentNode && (strcasecmp($this->parentNode->tagName, 'style') === 0 || strcasecmp($this->parentNode->tagName, 'script') === 0)) {
					$return = preg_replace('/^\s*(\/\/)?\<!\[CDATA\[|(\/\/)?\]\]\>\s*$/i', '', str_ireplace('</' . $this->tagName, '&lt;/' . $this->tagName, $this->nodeValue));
				} else {
					$return = self::escape($this->nodeValue);
				}
				break;

			// CDATA 节点
			case self::CDATA_SECTION_NODE:
				$return = '<![CDATA['. str_replace([']]>', '<![CDATA['], [']]&gt;', '&lt;![CDATA['], $this->nodeValue) .']]>';
				break;

			// 注释节点
			case self::COMMENT_NODE:
					// 过滤 防止 可能 的  if ie 属性出来
					$return = '<!--' . preg_replace('/(endif\s*)\]/i', '$1&#93;', preg_replace('/\[(\s*if|else)/i', '&#91;$1', preg_replace('/\](\s*>)/i', '&#93;$1',$this->nodeValue))) . '-->';
				break;
			// 根文档
			case self::DOCUMENT_NODE:
				$string = '';
				foreach ($this->childNodes as $node) {
					$node->format = $this->format;
					$string .= $node->__toString();
				}
				$return = $string;
				break;

			// 其他
			default:
				$return = NULL;
		}

		$this->format = false;
		return $return;
	}





	public function __get($name) {
		switch ($name) {
			case 'id':
			case 'name':
			case 'value':
				return $this->getAttribute($name);
				break;
			case 'className':
				return $this->getAttribute('class');
				break;
			case 'children':
				return $this->childNodes;
				break;
			case 'firstChild':
				return $this->childNodes ? reset($this->childNodes) : NULL;
				break;
			case 'lastChild':
				return $this->childNodes ? end($this->childNodes) : NULL;
				break;
			case 'nextSibling':
				if (!$this->parentNode) {
					return NULL;
				}
				$nextSibling = false;
				foreach ($this->parentNode->childNodes as $node) {
					if ($nextSibling) {
						return $node;
					}
					if ($node === $this) {
						$nextSibling = true;
					}
				}
				return NULL;
				break;
			case 'nodeName':
				switch ($this->nodeType) {
					case self::ELEMENT_NODE:
						return $this->tagName;
						break;
					case self::TEXT_NODE:
						return '#text';
						break;
					case self::CDATA_SECTION_NODE:
						return  '#cdata-section';
						break;
					case self::COMMENT_NODE:
						return '#comment';
						break;
					case self::DOCUMENT_NODE:
						return '#document';
						break;
					default:
						return NULL;
				}
				break;
			case 'outerHTML':
				switch ($this->nodeType) {
					case self::ELEMENT_NODE:
					case self::DOCUMENT_NODE:
						return $this->__toString();
						break;
					default:
						return NULL;
				}
			case 'innerHTML':
				switch ($this->nodeType) {
					case self::ELEMENT_NODE:
						if (isset(self::$singleTags[$this->tagName])) {
							return NULL;
						}
						$outerHTML = '';
						foreach ($this->childNodes as $node) {
							if ($this->format !== false) {
								$node->format = $this->format + 1;
							}
							$outerHTML .= $node->__toString();
						}
						return $outerHTML;
						break;
					case self::DOCUMENT_NODE:
						return $this->__toString();
						break;
					default:
						return NULL;
				}
				break;
			case 'outerText':
			case 'innerText':
			case 'textContent':
				switch ($this->nodeType) {
					case self::ELEMENT_NODE:
						if (isset(self::$singleTags[$this->tagName])) {
							return NULL;
						}
						$textContent = '';
						foreach ($this->childNodes as $node) {
							$textContent .= $node->textContent;
						}
						return $textContent;
						break;
					case self::TEXT_NODE:
						return $this->nodeValue;
						break;
					case self::COMMENT_NODE:
						return NULL;
						break;
					case self::DOCUMENT_NODE:
						$textContent = '';
						foreach ($this->childNodes as $node) {
							$textContent .= $node->textContent;
						}
						return $textContent;
						break;
					default:
						return NULL;
				}
				break;
			case 'parentElement':
				if ($this->parentNode && $this->parentNode->nodeType === self::ELEMENT_NODE) {
					return $this->parentNode;
				}
				return NULL;
				break;
			default:
				return NULL;
		}
	}

	public function __set($name, $value) {
		switch ($name) {
			case 'id':
			case 'name':
			case 'value':
				$this->setAttribute($name, $value);
				break;
			case 'className':
				$this->setAttribute('class', $name);
				break;
			case 'outerHTML':
				if ($this->parentNode) {
					if ($value !== false && $value !== '' && $value !== NULL) {
						$elements = new Node($value);
						foreach ($elements->childNodes as $element) {
							$this->parentNode->insertBefore($element, $this);
						}
					}
					$this->parentNode->removeChild($this);
				} elseif ($this->nodeType === self::DOCUMENT_NODE) {
					foreach ($this->childNodes as $key => $node) {
						$node->parentNode = NULL;
						unset($this->childNodes[$key]);
					}
					if ($value !== false && $value !== '' && $value !== NULL) {
						$elements = new Node($value);
						foreach ($elements->childNodes as $element) {
							$this->insertBefore($element);
						}
					}
				}
				break;
			case 'innerHTML':
				foreach ($this->childNodes as $key => $node) {
					$node->parentNode = NULL;
					unset($this->childNodes[$key]);
				}
				if ($value !== false && $value !== '' && $value !== NULL) {
					$elements = new Node($value);
					foreach ($elements->childNodes as $element) {
						$this->appendChild($element);
					}
				}
				break;
			case 'outerText';
				if ($this->parentNode) {
					if ($value !== false && $value !== '' && $value !== NULL) {
						$this->parentNode->insertBefore(self::createTextNode($value), $this);
					}
					$this->parentNode->removeChild($this);
				}
				break;
			case 'innerText':
			case 'textContent':
				switch ($this->nodeType) {
					case self::ELEMENT_NODE:
					case self::DOCUMENT_NODE:
						if (isset(self::$singleTags[$this->tagName])) {
							return NULL;
						}
						foreach ($this->childNodes as $key => $node) {
							$node->parentNode = NULL;
							unset($this->childNodes[$key]);
						}
						if ($value !== false && $value !== '' && $value !== NULL) {
							$this->appendChild(self::createTextNode($value));
						}
						break;
					case self::TEXT_NODE:
						$this->nodeValue = $value;
						break;
					default:
						return NULL;
				}
				break;
			default:
				$this->$name = $value;
		}
	}



	public function __clone() {
		$this->hash = spl_object_hash($this);
		// 复制
		foreach ($this->childNodes as &$value) {
			$value = clone $value;
		}
	}


	/**
	 * appendChild 在末尾插入子节点
	 * @param  Node   $node 节点对象
	 * @return Node   传入的参数
	 */
	public function appendChild(Node $node) {
		if ($node->parentNode) {
			$node->parentNode->removeChild($node);
		}
		$node->parentNode = $this;
		$this->childNodes[] = $node;
		return $node;
	}



	/**
	 * insertBefore 在某个节点之前插入子节点
	 * @param  Node        $node      插入的节点
	 * @param  Node|null   $reference 插入的节点紧跟着的节点
	 * @return Node   插入的那个节点
	 */
	public function insertBefore(Node $node, Node $reference = NULL) {
		if ($reference === NULL) {
			return $this->appendChild($node);
		}

		$childNodes = [];
		foreach($this->childNodes as $key => $value) {
			if ($value === $reference) {
				if ($node->parentNode) {
					$node->parentNode->removeChild($node);
				}
				$node->parentNode = $this;
				$childNodes[] = $node;
			}
			$childNodes[] = $value;
		}
		$this->childNodes = $childNodes;
		return $node;
	}




	/**
	 * replaceChild 替换节点
	 * @param  Node   $new 新节点
	 * @param  Node   $old 旧节点
	 * @return Node|false   返回旧节点
	 */
	public function replaceChild(Node $new, Node $old) {
		if (($index = array_search($old, $this->childNodes, true)) !== false) {
			$new->parentNode && $new->parentNode->removeChild($new);
			$this->childNodes[$index] = $new;
			$new->parentNode = $this;
			$old->parentNode = NULL;
			return $old;
		}
		return false;
	}



	/**
	 * removeChild 移除某个子节点
	 * @param  Node   $node 节点对象
	 * @return Node|false    返回移除的节点
	 */
	public function removeChild(Node $node) {
		if ($node->parentNode && ($index = array_search($node, $this->childNodes, true)) !== false) {
			unset($this->childNodes[$index]);
			$this->childNodes = array_values($this->childNodes);
			$node->parentNode = NULL;
			return $node;
		}
		return false;
	}



	public function getElementById($id) {
		return $this->querySelector('#' . $id);
	}


	public function getElementsByClassName($class) {
		return $this->querySelectorAll('.' . $class);
	}

	public function getElementsByName($name) {
		return $this->querySelectorAll('[name=\'' . $name . '\']');
	}

	public function getElementsByTagName($tagName) {
		return $this->querySelectorAll($tagName);
	}

	public function querySelector($selectors) {
		return ($nodes = $this->querySelectorAll($selectors)) ? reset($nodes) : false;
	}


	public function queryAll($selectors) {
		return  $this->querySelectorAll($selectors);
	}

	public function querySelectorAll($selectors) {
		$selectors = new Selectors($selectors);
		if (!$selectors->count()) {
			return [];
		}
		return array_values($this->quersyNodeMatch(array_map('array_reverse', $selectors->toArray())));
	}


	protected function quersyNodeMatch(array $selectors) {
		$all = $this->all();
		$hashAll = [];


		foreach ($selectors as $selector) {
			$mode = '';
			$results = [];
			foreach ($selector as $values) {
				if (is_array($values)) {
					switch ($mode) {
						case '':
							// 首个匹配过滤
							$results = array_filter($this->queryMatchValues($values, true));

							// 储存层次节点
							foreach ($results as $hash => &$result) {
								$result = [$hash => true];
							}
							unset($result);
							break;
						case ' ':
							// 多层次嵌套


							// 全部结果
							$_results = [];

							// queryMatchValues 匹配的结果
							$matches = [];


							do {
								$while = false;


								// 匹配元素
								foreach ($results as $currentHash => $resultsHash) {
									// 跳出了当前层次
									if (!$all[$currentHash]->parentNode->parentNode || empty($all[$all[$currentHash]->parentNode->parentNode->hash])) {
										unset($results[$currentHash]);
										continue;
									}

									$while = true;

									// 没匹配的进行匹配
									if (!isset($matches[$all[$currentHash]->parentNode->parentNode->hash])) {
										$matches[$all[$currentHash]->parentNode->parentNode->hash] = $all[$all[$currentHash]->parentNode->parentNode->hash]->queryMatchValues($values);
									}
								}


								// 储存点
								$__results = [];
								foreach ($results as $currentHash => $resultsHash) {
									$parentHash = $all[$currentHash]->parentNode->hash;

									if (empty($__results[$parentHash])) {
										$__results[$parentHash] = [];
									}
									$__results[$parentHash] += $resultsHash;


									// 不匹配的不储存到结果
									if (!$all[$currentHash]->parentNode->parentNode || empty($matches[$all[$currentHash]->parentNode->parentNode->hash][$parentHash])) {
										continue;
									}

									// 储存多个父级
									if (empty($_results[$parentHash])) {
										$_results[$parentHash] = [];
									}
									$_results[$parentHash] += $__results[$parentHash];
								}
								$results = $__results;
							} while($while);
							$results = $_results;
							break;
						case '>':
							// 单层次嵌套

							// queryMatchValues 匹配的结果
							$matches = [];


							// 匹配元素
							foreach ($results as $currentHash => $resultsHash) {
								// 跳出了当前层次
								if (!$all[$currentHash]->parentNode->parentNode || empty($all[$all[$currentHash]->parentNode->parentNode->hash])) {
									unset($results[$currentHash]);
									continue;
								}

								// 没匹配的进行匹配
								if (!isset($matches[$all[$currentHash]->parentNode->parentNode->hash])) {
									$matches[$all[$currentHash]->parentNode->parentNode->hash] = $all[$all[$currentHash]->parentNode->parentNode->hash]->queryMatchValues($values);
								}
							}



							// 储存点
							$_results = [];
							foreach ($results as $currentHash => $resultsHash) {
								$parentHash = $all[$currentHash]->parentNode->hash;

								// 不匹配的不储存到结果
								if (!$all[$currentHash]->parentNode->parentNode || empty($matches[$all[$currentHash]->parentNode->parentNode->hash][$parentHash])) {
									continue;
								}

								if (empty($_results[$parentHash])) {
									$_results[$parentHash] = [];
								}
								$_results[$parentHash] += $resultsHash;
							}

							$results = $_results;
							break;
						case '+':
							// 并列的上一个元素


							$matches = [];
							foreach ($results as $currentHash => $resultsHash) {
								$matches[$all[$currentHash]->parentNode->hash] = $all[$currentHash]->parentNode->queryMatchValues($values);
							}


							$_results = [];
							foreach ($results as $currentHash => $resultsHash) {
								$previousHash = false;
								foreach ($matches[$all[$currentHash]->parentNode->hash] as $hash => $boolean) {
									// 寻找到了当前了
									if ($hash === $currentHash) {
										if ($previousHash && $matches[$all[$currentHash]->parentNode->hash][$previousHash]) {
											$_results[$previousHash] = $resultsHash;
										}
										break;
									}
									// 储存上一个
									$previousHash = $hash;
								}
							}
							$results = $_results;
							break;
						case '~':
							// 并列前面有的
							$matches = [];
							foreach ($results as $currentHash => $resultsHash) {
								$matches[$all[$currentHash]->parentNode->hash] = $all[$currentHash]->parentNode->queryMatchValues($values);
							}



							$_results = [];
							foreach ($results as $currentHash => $resultsHash) {
								foreach ($matches[$all[$currentHash]->parentNode->hash] as $hash => $boolean) {
									// 到了当前就跳出
									if ($hash === $currentHash) {
										break;
									}

									// 匹配到
									if ($boolean) {
										if (empty($_results[$hash])) {
											$_results[$hash] = [];
										}
										$_results[$hash] += $resultsHash;
									}
								}
							}
							$results = $_results;
							break;
						default:
							$results = [];
							break;
					}
					if (!$results) {
						break;
					}
				} else {
					$mode = $values;
				}
			}
			foreach ($results as $result) {
				$hashAll += $result;
			}
		}

		return array_intersect_key($all, $hashAll);
	}


	/**
	 * queryMatch 匹配
	 * @param  array   $match      匹配数据
	 * @param  array   &$results   当前层次的结果
	 */
	protected function queryMatchValues(array $values, $recursive = false) {
		// 当前节点
		$nodes = [];
		$results = [];
		foreach ($this->childNodes as $node) {
			if ($node->nodeType !== self::ELEMENT_NODE) {
				continue;
			}
			$results[$node->hash] = true;
			$nodes[$node->hash] = $node;
		}
		if (!$results) {
			return $results;
		}

		foreach ($values as $value) {
			switch ($value[0]) {
				case '':
					// 标签名
					foreach ($nodes as $hash => $node) {
						if ($results[$hash]) {
							$results[$hash] = strcasecmp($value[1], $node->tagName) === 0;
						}
					}
					break;
				case '*':
					// 全部
					break;
				case '#':
					// id名
					foreach ($nodes as $hash => $node) {
						if ($results[$hash]) {
							$results[$hash] = $node->attributes['id'] === $value[1];
						}
					}
					break;
				case '.':
					// class 名
					foreach ($nodes as $hash => $node) {
						if ($results[$hash]) {
							$results[$hash] = ($class = $node->attributes['class']) && in_array($value[1], array_map('trim', explode(' ', $class)), true);
						}
					}
					break;
				case '[]':
					// 匹配属性
					foreach ($nodes as $hash => $node) {
						if ($results[$hash]) {
							$results[$hash] = false;
							foreach ($value[1] as $attributeName) {
								if ($value[2] === '*') {
									// 所有属性任意一个
									foreach ($node->attributes as $attributeName => $attributeValue) {
										if ($this->queryAttributeMatch($node, $attributeName, $value[3], $value[2])) {
											$results[$hash] = true;
											break;
										}
									}
								} elseif ($this->queryAttributeMatch($node, $attributeName, $value[3], $value[2])) {
									$results[$hash] = true;
									break;
								}
							}
						}
					}
					break;
				case ':':
					switch ($value[1]) {
						case 'root':
							// 跟目录
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = $this->nodeType === self::DOCUMENT_NODE && strcasecmp($node->tagName, 'html') === 0;
								}
							}
							break;
						case 'target':
							// 带有 target属性的
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = empty($node->attributes['target']);
								}
							}
							break;
						case 'lang':
							// lang 属性
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = ($lang = $node->attributes['lang']) && preg_match('/^'.preg_quote($value[2], '/').'(\-|$)/i', $lang);
								}
							}
							break;
						case 'empty':
							// 元素没有字元素的标签
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									foreach ($node->childNodes as $childNode) {
										if ($childNode->nodeType === self::ELEMENT_NODE) {
											$results[$hash] = false;
											break;
										}
									}
								}
							}
							break;
						case 'enabled':
							// 启用的表单
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = $node->attributes['disabled'] === NULL || $node->attributes['disabled'] === false;
								}
							}
							break;
						case 'disabled':
							// 禁用的表单
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = $node->attributes['disabled'] || $node->attributes['disabled'] === '';
								}
							}
							break;
						case 'required':
							// 带有 required 属性的
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = ($node->attributes['required'] || $node->attributes['required'] === '') && in_array(strtolower($node->tagName), ['input', 'select', 'textarea'], true);
								}
							}
							break;
						case 'optional':
							// 不带有 required 属性的
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = ($node->attributes['required'] === NULL || $node->attributes['required'] === false) && in_array(strtolower($node->tagName), ['input', 'select', 'textarea'], true);
								}
							}
							break;
						case 'read-only':
							// 带有 readonly 属性的
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = ($node->attributes['readonly'] || $node->attributes['readonly'] === '') && in_array(strtolower($node->tagName), ['input', 'select', 'textarea'], true);
								}
							}
							break;
						case 'read-write':
							// 不带有 readonly 属性的
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = ($node->attributes['readonly'] === NULL || $node->attributes['readonly'] === false) && in_array(strtolower($node->tagName), ['input', 'select', 'textarea'], true);
								}
							}
							break;
						case 'first-child':
							// 必须是首个子元素
							if ($results) {
								$reset = reset($results);
								foreach ($results as &$result) {
									$result = false;
								}
								unset($result);
								reset($results);
								$results[key($results)] = $reset;
							}
							break;
						case 'last-child':
							// 必须是最后一个元素
							if ($results) {
								$end = end($results);
								foreach ($results as &$result) {
									$result = false;
								}
								$result = $end;
								unset($result);
							}
							break;
						case 'only-child':
							// 必须是第一个元素 也必须是最后一个元素
							$is = count($results) === 1;
							foreach ($results as &$result) {
								$result = $is && $result;
							}
							unset($result);
							break;
						case 'first-of-type':
							// "匹配到的"第一个元素
							$first = true;
							foreach ($results as &$result) {
								if ($result) {
									$result = $first;
									$first = false;
								}
							}
							unset($result);
							break;
						case 'last-of-type':
							// "匹配到的"最后一个元素
							$endHash = false;
							foreach ($results as $hash => &$result) {
								if ($result) {
									$hash = $endHash;
									$result = false;
								}
							}
							if ($endHash) {
								$results[$endHash] = true;
							}
							unset($result);
							break;
						case 'only-of-type':
							// "匹配到的"第一个也是最后一个
							$i = 0;
							if (count(array_intersect($result, [true])) > 1) {
								foreach ($results as &$result) {
									$result = false;
								}
								unset($result);
							}
							break;
						case 'nth-child':
							// 的第几个元素
							$n = $value[2][2];
							foreach ($results as &$result) {
								++$n;
								if ($n !== $value[2][0] && (!$value[2][1] || ($n % $value[2][0]) !== 0)) {
									$result = false;
								}
							}
							unset($result);
							break;
						case 'nth-last-child':
							// 倒序的第几个元素
							$n = $value[2][2];
							$results = array_reverse($results, true);
							foreach ($results as &$result) {
								++$n;
								if ($n !== $value[2][0] && (!$value[2][1] || ($n % $value[2][0]) !== 0)) {
									$result = false;
								}
							}
							unset($result);
							$results = array_reverse($results, true);
							break;
						case 'nth-of-type':
							// "匹配到的"第几个元素
							$n = $value[2][2];
							foreach ($results as &$result) {
								if ($result) {
									++$n;
									if ($n !== $value[2][0] && (!$value[2][1] || ($n % $value[2][0]) !== 0)) {
										$result = false;
									}
								}
							}
							unset($result);
							break;
						case 'nth-last-of-type':
							// "匹配到的"倒序第几个元素
							$results = array_reverse($results, true);
							$n = $value[2][2];
							foreach ($results as &$result) {
								if ($result) {
									++$n;
									if ($n !== $value[2][0] && (!$value[2][1] || ($n % $value[2][0]) !== 0)) {
										$result = false;
									}
								}
							}
							unset($result);
							$results = array_reverse($results, true);
							break;
						case 'has':
							// 子元素存在 匹配 不能嵌套 matches
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = $node->quersyNodeMatch(array_map('array_reverse', $value[2]->toArray())) ? true : false;
								}
							}
							break;
						case 'not':
							// 子元素存在 忽略 不能嵌套 matches
							foreach ($nodes as $hash => $node) {
								if ($results[$hash]) {
									$results[$hash] = $node->quersyNodeMatch(array_map('array_reverse', $value[2]->toArray())) ? false : true;
								}
							}
							break;
						case 'matches':
							// or 匹配
							foreach ($results as &$result) {
							 	$result = false;
							}
							unset($result);
							break;
						default:
							foreach ($results as &$result) {
							 	$result = false;
							}
							unset($result);
					}
					break;
				default:
					foreach ($results as &$result) {
					 	$result = false;
					}
					unset($result);
					break;
			}
		}
		if ($recursive) {
			foreach ($nodes as $node) {
				$results += $node->queryMatchValues($values, $recursive);
			}
		}
		return $results;
	}


	/**
	 * queryAttributeMatch 属性匹配
	 * @param  Node   $node 节点
	 * @param  string $attributeName 属性名
	 * @param  string $attributeValue 属性值
	 * @param  string $compare 运算符
	 * @return boolean
	 */
	protected function queryAttributeMatch(Node $node, $attributeName, $attributeValue, $compare) {
		// 不区分大小写的字段
		static $cases = ['lang' => true, 'target' => true];
		if (!isset($node->attributes[$attributeName])) {
			return false;
		}
		switch ($compare) {
			case '':
				// 存在
				return $node->attributes[$attributeName] !== NULL && $node->attributes[$attributeName] !== false;
				break;
			case '=':
				// 等于
				if (isset($cases[$attributeName])) {
					return strcasecmp($node->attributes[$attributeName], $attributeValue) === 0;
				} else {
					return $node->attributes[$attributeName] === $attributeValue;
				}
				break;
			case '~=':
				// 包含单词
				return preg_match('/(^|\s+)?'. preg_quote($attributeValue, '/') .'(\s+|$)/' . (isset($cases[$attributeName]) ? 'i' : ''));
				break;
			case '|=':
				//  等于 或  value-xxxx
				return preg_match('/^'. preg_quote($attributeValue, '/') .'(-|$)/' . (isset($cases[$attributeName]) ? 'i' : ''));
				break;
			case '^=':
				// 开头
				if (isset($cases[$attributeName])) {
					return stripos($node->attributes[$attributeName], $attributeValue) === 0;
				} else {
					return strpos($node->attributes[$attributeName], $attributeValue) === 0;
				}
				break;
			case '$=':
				// 结尾
				return preg_match('/'. preg_quote($attributeValue, '/') .'$/' . (isset($cases[$attributeName]) ? 'i' : ''));
				break;
			case '*=':
				// 之中
				if (isset($cases[$attributeName])) {
					return stripos($node->attributes[$attributeName], $attributeValue) !== false;
				} else {
					return strpos($node->attributes[$attributeName], $attributeValue) !== false;
				}
				break;
			default:
				return true;
		}
		return false;
	}






















	/**
	 * getAttribute 去的一个属性
	 * @param  string $name 属性名
	 * @return string|boolean|null
	 */
	public function getAttribute($name) {
		return $this->attributes[$name];
	}


	/**
	 * setAttribute 设置一个属性
	 * @param string              $name 属性名
	 * @param string|boolean|null $value 属性值
	 * @return string|boolean|null
	 */
	public function setAttribute($name, $value) {
		$this->attributes[$name] = $value;
		return true;
	}

	/**
	 * hasAttribute 某个属性是否存在
	 * @param  string   $name 属性名
	 * @return boolean
	 */
	public function hasAttribute($name) {
		return $this->attributes[$name] !== NULL;
	}


	/**
	 * removeAttribute 移除一个属性
	 * @param  string   $name 属性名
	 * @return boolean
	 */
	public function removeAttribute($name) {
		unset($this->attributes[$name]);
		return true;
	}



	public static function createElement($tagName) {
		return new Node($tagName, self::ELEMENT_NODE);
	}

	public static function createTextNode($text) {
		return new Node($text, self::TEXT_NODE);
	}


	public static function createCDATASection($data) {
		return new Node($data, self::CDATA_SECTION_NODE);
	}

	public static function createComment($comment) {
		return new Node($comment, self::COMMENT_NODE);
	}



	public static function escape($string) {
		return str_replace(['"', '\'', '<', '>'], ['&quot;', '&#039;', '&lt;', '&gt;'], $string);
	}



	/**
	 * compact 压缩文档
	 * @return boolean
	 */
	public function compact() {
		switch ($this->nodeType) {
			case self::DOCUMENT_NODE:
			case self::ELEMENT_NODE:
				foreach($this->childNodes as $node) {
					if (strcasecmp($node->tagName, 'pre') !== 0) {
						$node->compact();
					}
				}
				break;
			case self::TEXT_NODE:
			case self::COMMENT_NODE:
				if ($this->parentNode && !trim($this->nodeValue)) {
					$this->parentNode->removeChild($this);
				}
				break;
		}
	}


	/**
	 * all 返回全部节点
	 * @return array
	 */
	public function all() {
		$all = [];
		if ($this->nodeType === self::ELEMENT_NODE || $this->nodeType === self::DOCUMENT_NODE) {
			$all[$this->hash] = $this;
			if ($this->childNodes) {
				foreach ($this->childNodes as $node) {
					$all += $node->all();
				}
			}
		}
		return $all;
	}


	protected function cspn($search, $buffer = true) {
		$result = strcspn($this->string, $search, $this->offset);
		if ($buffer) {
			$this->buffer .= substr($this->string, $this->offset, $result);
		}
		$result += $this->offset;
		if ($result >= $this->length) {
			$this->offset = $result;
			return false;
		}
		$this->offset = $result + 1;
		return $this->string{$result};
	}


	protected function pos($search, $buffer = true, $ipos = false) {
		$result = $ipos ? stripos($this->string, $search, $this->offset) : strpos($this->string, $search, $this->offset);
		$offset = $result === false ? $this->length : $result + strlen($search);
		if ($buffer) {
			$this->buffer .= $result === false ? substr($this->string, $this->offset) : substr($this->string, $this->offset, $offset - $this->offset - strlen($search));
		}
		$this->offset = $offset;
		return $result === false ? false : $search;
	}





	public function offsetSet($name, $value) {
		if ($value === NULL || $value === false) {
			if ($name !== NULL) {
				unset($this->childNodes[$name]);
			}
		} elseif (isset($this->childNodes[$name])) {
			if ($value instanceof Node) {
				$this->replaceChild($value, $this->childNodes[$name]);
			}
		} elseif ($value instanceof Node) {
			$this->appendChild($value);
		}
	}
	public function offsetExists($name) {
		return isset($this->childNodes[$name]);
	}
	public function offsetUnset($name) {
		unset($this->childNodes[$name]);
	}

	public function offsetGet($name) {
		return isset($this->childNodes[$name]) ? $this->childNodes[$name] : NULL;
	}


	public function format($format) {
		$this->format = $format ? 0 : false;
		return $this;
	}


	public function getIterator() {
		return new ArrayIterator($this->childNodes);
	}

	public function count() {
		return count($this->childNodes);
	}

	public function jsonSerialize() {
		$json = ['nodeType' => $this->nodeType, 'nodeValue' => $this->nodeValue, 'attributes' => $this->attributes, 'tagName' => $this->tagName];
		foreach ($this->childNodes as $node) {
			$json['childNodes'][] = $node->jsonSerialize();
		}
		return $json;
	}
}