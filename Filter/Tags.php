<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-01 14:00:25
/*	Updated: UTC 2015-07-22 05:35:59
/*
/* ************************************************************************** */
namespace Loli\DOM\Filter;
use Loli\DOM\Node, Loli\DOM\CSS\Rule;
class Tags{

	// 所有允许的标签
	protected $tags = [
		'a' => true, 'abbr' => true, 'acronym' => true, 'address' => true, 'applet' => true, 'area' => true, 'article' => true, 'aside' => true, 'audio' => true,
		'b' => true, 'base' => true, 'basefont' => true, 'bdi' => true, 'bdo' => true, 'big' => true, 'blockquote' => true, 'blockcode' => true, 'body' => true, 'br' => true, 'button' => true,
		'canvas' => true, 'caption' => true, 'center' => true, 'cite' => true, 'code' => true, 'col' => true, 'colgroup' => true, 'command' => true,
		'datalist' => true, 'dd' => true, 'del' => true, 'details' => true, 'dfn' => true, 'dir' => true, 'div' => true, 'dl' => true, 'dt' => true, 'dialog' => true,
		'em' => true, 'embed' => true,
		'fieldset' => true, 'figcaption' => true, 'figure' => true, 'font' => true, 'footer' => true, 'form' => true, 'frame' => true, 'frameset' => true,
		'h1' => true, 'h2' => true, 'h3' => true, 'h4' => true, 'h5' => true, 'h6' => true, 'head' => true, 'header' => true, 'hgroup' => true, 'hr' => true, 'html' => true,
		'i' => true, 'iframe' => true, 'img' => true, 'input' => true, 'ins' => true,
		'keygen' => true, 'kbd' => true,
		'label' => true, 'legend' => true, 'li' => true, 'link' => true,
		'map' => true, 'mark' => true, 'menu' => true, 'menuitem' => true, 'meta' => true, 'meter' => true,
		'nav' => true, 'noframes' => true, 'noscript' => true,
		'object' => true, 'ol' => true, 'optgroup' => true, 'option' => true, 'output' => true,
		'p' => true, 'param' => true, 'pre' => true, 'progress' => true, 'polygon' => true,
		'q' => true,
		'rp' => true, 'rt' => true, 'ruby' => true,
		's' => true, 'samp' => true, 'script' => true, 'select' => true, 'small' => true, 'source' => true, 'span' => true, 'strike' => true, 'strong' => true, 'style' => true, 'sub' => true, 'summary' => true, 'sup' => true, 'svg' => true,
		'table' => true, 'tbody' => true, 'td' => true, 'textarea' => true, 'tfoot' => true, 'th' => true, 'thead' => true, 'time' => true, 'title' => true, 'tr' => true, 'track' => true, 'tt' => true,
		'u' => true, 'ul' => true,
		'var' => true, 'video' => true,
		'wbr' => true,
		'xmp' => true,


		'base' => NULL, 'html' => NULL, 'meta' => NULL, 'link' => NULL, 'script' => NULL, 'head' => NULL, 'body' => NULL, 'title' => NULL, 'noframes' => NULL, 'noscript' => NULL, 'frameset' => NULL, 'frame' => NULL, 'iframe' => NULL, 'applet' => NULL, 'polygon' => NULL, 'svg' => NULL, 'dialog' => NULL,
		'command' => NULL,
	];

	// 下面是特殊的 html 标签
	// 'base', 'html', 'meta', 'link', 'script', 'style', 'head', 'body', 'title', 'noframes', 'noscript', 'frameset', 'frame', 'iframe', 'applet', 'polygon', 'svg', 'dialog'

	// 扩展插件
	// 'object', 'embed'
	// 表单
	// 'form', 'input', 'select', 'option', 'textarea', 'button', 'command', 'keygen', 'output',


	// 所有 内联元素
	protected $inlineTags = [
		'a' => true,
		'abbr' => true,
		'acronym' => true,
		'audio' => true,
		'b' => true,
		'bdo' => true,
		'big' => true,
		'br' => true,
		'cite' => true,
		'code' => true,
		'dfn' => true,
		'em' => true,
		'font' => true,
		'i' => true,
		'img' => true,
		'input' => true,
		'kbd' => true,
		'label' => true,
		'q' => true,
		's' => true,
		'samp' => true,
		'select' => true,
		'small' => true,
		'span' => true,
		'strike' => true,
		'strike' => true,
		'strong' => true,
		'sub' => true,
		'sup' => true,
		'svg' => true,
		'textarea' => true,
		'tt' => true,
		'time' => true,
		'meter' => true,
		'option' => true,
		'u' => true,
		'var' => true,
		'video' => true,
	];




	// 所有 行内块元素
	protected $inlineBlockTags = [
		'applet' => true,
		'button' => true,
		'del' => true,
		'iframe' => true,
		'frame' => true,
		'ins' => true,
		'map' => true,
		'object' => true,
		'param' => true,
		'script' => true,
	];



	// 所有 单标签 无结束标签的
	protected $singleTags = [
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





	// 不允许嵌套块元素的 块元素
	protected $blockNotNestedTags = [
		'h1' => true,
		'h2' => true,
		'h3' => true,
		'h4' => true,
		'h5' => true,
		'h6' => true,
		'p' => true,
		'dd' => true,
		'dt' => true,
	];




	// 不允许嵌套 自己的 元素
	protected $notNestedSelfTags = [
		'a' => true,
		'dd' => true,
		'dt' => true,
		'button' => true,
		'option' => true,
		'optgroup' => true,
		'form' => true,
		'frame' => true,
		'label' => true,
		'pre' => true,
		'embed' => true,
		'object' => true,
	];





	// 字符串标签
	protected $textTags = [
		'style' => true,
		'script' => true,
		'textarea' => true,
	];

	// 当前标签允许的父级
	protected $allowParentTags = [
		'li' => ['ul', 'ol', 'nl'],
		'dt' => ['dl'],
		'dd' => ['dl'],
		'thead' => ['table'],
		'tbody' => ['table'],
		'caption' => ['table'],
		'colgroup' => ['table'],
		'col' => ['table', 'colgroup'],
		'tr' => ['table', 'tbody'],
		'td' => ['tr'],
		'th' => ['tr'],
		'param' => ['object', 'applet'],
	];

	// 当前标签允许的子级  设置了子级不允许text
	protected $allowChildTags = [
		'ul' => ['li'],
		'ol' => ['li'],
		'nl' => ['li'],
		'dl' => ['dt', 'dd'],
		'table' => ['thead', 'tbody', 'tr', 'caption', 'colgroup', 'col'],
		'thead' => ['tr'],
		'tbody' => ['tr'],
		'tr' => ['td', 'th'],
		'object' => ['param', 'embed'],
	];




	// 当前标签 不允许的子级 (单层次)
	protected $tagsSinglesLevel = [
		'li' => ['li'],
		'tr' => ['tr'],
	];


	// 当前标签  不允许的子级 (多层次)
	protected $tagsMultisLevel = [
		'a' => ['a', 'button' , 'input', 'form', 'textarea'],
		'button' => ['textarea' , 'input' , 'button' , 'select' , 'label' , 'form' , 'fieldset' , 'iframe'],
		'frame' => ['frame'],
		'form' => ['form'],
		'label' => ['label'],
		'pre' => ['img', 'object', 'embed', 'big', 'samll', 'sub', 'sup', 'pre'],
	];

	// style 方法
	protected $style;

	protected $prefix = 'content-';

	public function __construct(Style $style = NULL) {
		if ($style) {
			$this->style = $style;
			$this->prefix =& $style->prefix;
		}
	}

	public function __invoke() {
		call_user_func_array([$this, 'filters'], func_get_args());
	}

	/**
	 * filters 过滤
	 * @param  Node   $node
	 */
	public function filters(Node $node) {

		// 过滤未知标签
		$this->removeTags($node);

		// 添加默认标签
		$this->defaultTags($node);

		// 并列标签
		$this->abreastTags($node);

		// 当前标签允许的父级
		$this->allowParentTags($node);

		// 当前标签允许子级
		$this->allowChildTags($node);

		// 禁止嵌套自己的标签
		$this->notNestedSelfTags($node, $node);

		// 禁止嵌套块元素的标签
		$this->blockNotNestedTags($node, $node);

		// 样式表的值过滤
		$this->styleValue($node);

		gc_collect_cycles();
	}





	/**
	 * allowParentTags 当前标签允许嵌套的父级
	 * @param  Node   $node 节点
	 */
	protected function allowParentTags(Node $node) {
		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType === Node::ELEMENT_NODE) {
				if ($childNode->nodeType === Node::ELEMENT_NODE && isset($this->allowParentTags[$tagName = strtolower($childNode->tagName)]) && ($node->nodeType !== Node::ELEMENT_NODE || !in_array(strtolower($node->tagName), $this->allowParentTags[$tagName], true))) {
					// 不允许的删除节点
					$childNode->parentNode->removeChild($childNode);
				} else {
					// 否则递归
					$this->allowParentTags($childNode);
				}
			}
		}
	}

	/**
	 * allowChildTags 当前标签允许嵌套的子级
	 * @param  Node   $node 节点
	 */
	protected function allowChildTags(Node $node) {
		foreach ($node->childNodes as $childNode) {
			switch ($childNode->nodeType) {
				case Node::COMMENT_NODE:
					// 注释跳过
					break;
				case Node::ELEMENT_NODE:
					if ($node->nodeType === Node::ELEMENT_NODE && isset($this->allowChildTags[$tagName = strtolower($node->tagName)]) && !in_array(strtolower($childNode->tagName), $this->allowParentTags[$tagName], true)) {
						// 不允许的删除节点
						$childNode->parentNode->removeChild($childNode);
					} else {
						// 否则递归
						$this->allowParentTags($childNode);
					}
					// 元素
					break;
				default:
					// 其他删除
					$childNode->parentNode->removeChild($childNode);
			}
		}
	}






	/**
	 * removeTags 删除未知标签 不允许的标签
	 * @param  Node   $node 节点
	 */
	protected function removeTags(Node $node) {
		foreach ($node->childNodes as $childNode) {
			switch ($childNode->nodeType) {
				case Node::COMMENT_NODE:
				case Node::TEXT_NODE:
					// 文档 字符串 注释 跳过
					break;
				case Node::ELEMENT_NODE:
					// 元素节点
					$tagName = strtolower($childNode->tagName);

					if (isset($this->tags[$tagName])) {
						// 递归
						$this->removeTags($childNode);
					} else {
						//  不是文本数据
						if (!isset($this->textTags[$tagName])) {
							// 递归
							$this->removeTags($childNode);

							// 写入
							foreach ($childNode->childNodes as $childNode2) {
								$childNode->parentNode->insertBefore($childNode2, $childNode);
							}
						}
						$childNode->parentNode->removeChild($childNode);
					}
					break;
				default:
					//其他节点 移除
					$childNode->parentNode->removeChild($childNode);
			}
		}
	}





	/**
	 * defaultTags 添加默认标签
	 * @param  Node   $node 节点
	 */
	protected function defaultTags(Node $node) {
		$array = [];

		foreach ($node->childNodes as $childNode) {
			if ($array) {
				array_pop($array);
				continue;
			}

			while ($childNode && ($childNode->nodeType === Node::TEXT_NODE || ($childNode->nodeType === Node::ELEMENT_NODE && isset($this->inlineTags[strtolower($childNode->tagName)])) || ($array && $childNode->nodeType === Node::COMMENT_NODE))) {
				$array[] = $childNode;
				$childNode = $childNode->nextSibling;
			}

			if ($array) {
				$childNode = reset($array);
				$element = Node::createElement('p');
				$childNode->parentNode->insertBefore($element, $childNode);
				foreach ($array as $value) {
					$element->appendChild($value);
				}
				array_pop($array);
			}
		}
	}

	/**
	 * abreastTags 并列标签
	 * @param  Node   $node node节点
	 */
	protected function abreastTags(Node $node) {
		$array = [];
		$block = false;
		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType === Node::ELEMENT_NODE && $array && !isset($this->inlineBlockTags[$tagName = strtolower($childNode->tagName)]) && !isset($this->inlineTags[$tagName])) {
				// 并列
				$element = Node::createElement($tagName === 'div' ? 'div' : 'p');
				$childNode->parentNode->insertBefore($element, $childNode);
				foreach ($array as $value) {
					$element->appendChild($value);
				}
				$block = $childNode;
				$array = [];
			} elseif (($array && $childNode->nodeType === Node::COMMENT_NODE) || ($childNode->nodeType === Node::ELEMENT_NODE && isset($this->inlineTags[strtolower($childNode->tagName)]))) {
				// 储存上一个
				$array[] = $childNode;
			} else {
				// 清空上一个
				$array = [];
			}

			// 递归
			if ($childNode->nodeType === Node::ELEMENT_NODE) {
				$this->abreastTags($childNode);
			}
		}


		// 最后一个标签 也要设置
		if ($array && $block) {
			$element = Node::createElement(strtolower($block->tagName) === 'div' ? 'div' : 'p');
			$block->parentNode->insertBefore($element, $block);
			foreach ($array as $value) {
				$element->appendChild($value);
			}
		}
	}


	/**
	 * notNestedSelfTags 不允许嵌套自己的标签
	 * @param  Node   $node     节点
	 * @param  Node   $nodeRoot 跟节点
	 */
	protected function notNestedSelfTags(Node $node, Node $nodeRoot) {
		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType !== Node::ELEMENT_NODE) {
				continue;
			}
			if (isset($this->notNestedSelfTags[$tagName = strtolower($childNode->tagName)])) {
				$currentNode = $childNode;
				while($currentNode !== $nodeRoot && $currentNode->parentNode) {
					$currentNode = $currentNode->parentNode;
					if ($currentNode->nodeType !== Node::ELEMENT_NODE) {
						break;
					}
					// 标签相同
					if (strtolower($currentNode->tagName) === $tagName) {
						foreach ($childNode->childNodes as $value) {
							$childNode->parentNode->insertBefore($value, $childNode);
						}
						$childNode->parentNode->removeChild($childNode);
						$this->notNestedSelfTags($node, $nodeRoot);
						return;
					}
				}
			}
			// 递归
			$this->notNestedSelfTags($childNode, $nodeRoot);
		}
	}

	protected function blockNotNestedTags(Node $node, Node $nodeRoot) {

		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType !== Node::ELEMENT_NODE) {
				continue;
			}

			if (isset($this->blockNotNestedTags[$tagName = strtolower($childNode->tagName)])) {
				$currentNode = $childNode;
				while($currentNode !== $nodeRoot && $currentNode->parentNode) {
					$currentNode = $currentNode->parentNode;
					if ($currentNode->nodeType !== Node::ELEMENT_NODE) {
						break;
					}
					// 是块元素
					if (!isset($this->inlineBlockTags[$tagName2 = strtolower($currentNode->tagName)]) && !isset($this->inlineTags[$tagName2])) {
						foreach ($childNode->childNodes as $value) {
							$childNode->parentNode->insertBefore($value, $childNode);
						}
						$childNode->parentNode->removeChild($childNode);

						$this->blockNotNestedTags($node, $nodeRoot);
						return;
					}
				}
			}

			// 递归
			$this->blockNotNestedTags($childNode, $nodeRoot);
		}
	}

	protected function styleValue($node) {
		foreach ($node->childNodes as $childNode) {
			if ($childNode->nodeType !== Node::ELEMENT_NODE) {
				continue;
			}
			if (strcasecmp($childNode->tagName, 'style') !== 0) {
				$this->styleValue($childNode);
				continue;
			}
			if (!$this->style) {
				$childNode->parentNode->removeChild($childNode);
				continue;
			}
			$rule = new Rule($childNode->textContent);
			$this->styleRule($rule);
			$childNode->textContent = (string) $rule->format(true);
			if (!$childNode->attributes['type']) {
				$childNode->attributes['type'] = 'text/css';
			}
		}
	}


	protected function styleRule(Rule $rules) {
		foreach ($rules->cssRules as $rule) {
			switch ($rule->type) {
				case Rule::STYLE_RULE:
					// 样式表过滤

					if (!$rule->selectorText->count()) {
						$rules->deleteRule($rule);
						break;
					}

					if ($this->prefix) {
						foreach ($rule->selectorText->toArray() as $selector) {
							$value = $selector[0][0];
							if (!in_array($value[0], ['#', '.']) || substr($value[1], 0, strlen($this->prefix)) !== $this->prefix) {
								$rules->deleteRule($rule);
								break 2;
							}
						}
					}

					foreach ($rule->cssRules as $value) {
						switch ($value->type) {
							case Rule::PROPERTY_RULE:
								if (!$this->style->filrer($value->name, $value->value)) {
									$rule->deleteRule($value);
								}
								break;
							case Rule::COMMENT_RULE:
								// 保留注释
								break;
							default:
								$rule->deleteRule($value);
						}
					}
					break;
				case Rule::KEYFRAMES_RULE:
					// 动画名过滤

					if ($this->prefix && substr($rule->name, 0, strlen($this->prefix)) !== $this->prefix) {
						$rules->deleteRule($rule);
						break;
					}
					foreach ($rule->cssRules as $value) {
						switch ($value->type) {
							case Rule::KEYFRAME_RULE:
								foreach ($value->cssRules as $value2) {
									switch ($value2->type) {
										case Rule::PROPERTY_RULE:
											if (!$this->style->filrer($value2->name, $value2->value)) {
												$value->deleteRule($value2);
											}
											break;
										case Rule::COMMENT_RULE:
											// 保留注释
											break;
										default:
											$value->deleteRule($value2);
									}
								}
								break;
							case Rule::COMMENT_RULE:
								// 保留注释
								break;
							default:
								$rule->deleteRule($value);
						}
					}
					break;
				case Rule::MEDIA_RULE:
				case Rule::SUPPORTS_RULE:
					$this->styleRule($rule);
					break;
				case Rule::COMMENT_RULE:
					// 保留注释
					break;
				default:
					$rules->deleteRule($rule);
			}
		}
	}
}