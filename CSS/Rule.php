<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-18 07:58:07
/*	Updated: UTC 2015-07-21 10:19:41
/*
/* ************************************************************************** */
namespace Loli\DOM\CSS;
use ArrayAccess, Countable, IteratorAggregate, ArrayIterator;
class Rule extends Base implements ArrayAccess, IteratorAggregate, Countable{
	const STYLE_RULE = 1;
	const CHARSET_RULE = 2;
	const IMPORT_RULE = 3;
	const MEDIA_RULE = 4;
	const FONT_FACE_RULE = 5;
	const PAGE_RULE = 6;
	const KEYFRAMES_RULE = 7;
	const KEYFRAME_RULE = 8;
	const MARGIN_RULE = 9;
	const NAMESPACE_RULE = 10;
	const COUNTER_STYLE_RULE = 11;
	const SUPPORTS_RULE = 12;
	const DOCUMENT_RULE = 13;
    const FONT_FEATURE_VALUES_RULE = 14;
    const VIEWPORT_RULE = 15;
	const CUSTOM_MEDIA_RULE = 17;



	const ROOT_RULE = 97;

	const PROPERTY_RULE = 98;

	const COMMENT_RULE = 99;

	const FONT_FEATURE_VALUES_MAP_RULE = 100;


	// 嵌套层次限制
	const NESTING = 10;

	const BLANK = " \t\n\r\0\x0B";

	// 节点前缀
	public $privatePrefix = '';


	// 节点类型
	protected $type;

	// 选择器
	protected $selectorText;

	// conditionText
	protected $conditionText;

	// media
	protected $media;

	// 格式化
	protected $format = '';

	//  父级规则
	protected $parentRule;

	// css 子规则
	public $cssRules = [];



	// @font-face 字体文件属性名字等
	protected static $fontFacePropertys = ['family' => 'font-family', 'src' => 'src', 'unicodeRange' => 'unicode-range',  'variant' => 'font-variant', 'featureSettings' => 'font-feature-settings', 'stretch' => 'font-stretch', 'weight' => 'font-weight', 'style' => 'font-style'];

	// @viewport 属性
	protected static $viewportPropertys = ['min-width', 'max-width', 'width', 'min-height', 'max-height', 'height', 'zoom', 'min-zoom', 'max-zoom', 'user-zoom', 'orientation'];

	// @counter-style 属性
	protected static $counterStylePropertys = ['system' => 'system', 'symbols' => 'symbols', 'additiveSymbols' => 'additive-symbols', 'negative' => 'negative', 'prefix' => 'prefix', 'suffix' => 'suffix', 'range' => 'range', 'pad' => 'pad', 'speakAs' => 'speak-as', 'fallback' => 'fallback'];

	// @font-feature-values 名字
	protected static $fontFeatureValuesNames = ['swash' => 'swash', 'annotation' => 'annotation', 'ornaments' => 'ornaments', 'stylistic' => 'stylistic', 'characterVariant' => 'character-variant', 'styleset' => 'styleset'];

	public function __construct($value = false, $type = false) {
		// http://dev.w3.org/csswg/cssom/#idl-index
		// This version:
		// http://www.w3.org/TR/2013/CR-css3-conditional-20130404/
		// Latest version:
		// http://www.w3.org/TR/css3-conditional/
		// Editor's draft:
		// http://dev.w3.org/csswg/css3-conditional/ (change log, older change log)
		// Previous version:
		// http://www.w3.org/TR/2012/WD-css3-conditional-20121213/

		switch ($type) {
			case self::STYLE_RULE:
				// http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSStyleRule
				// http://dev.w3.org/csswg/cssom/#the-cssstylerule-interface
				// http://dev.w3.org/csswg/cssom-1/#cssstylerule

				$this->type = self::STYLE_RULE;
				$this->selectorText = $value instanceof Selectors ? $value : new Selectors($value);
				break;
			case self::CHARSET_RULE:
				// http://www.w3.org/TR/cssom/#the-csscharsetrule-interface

				$this->type = self::CHARSET_RULE;
				$this->encoding = $value;
				break;
			case self::IMPORT_RULE:
				// http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSImportRule
				// http://dev.w3.org/csswg/cssom/#the-cssimportrule-interface
				// http://dev.w3.org/csswg/cssom-1/#the-cssimportrule-interface

				$this->type = self::IMPORT_RULE;
				$this->href = $value;
				$this->media = new Media;
				break;
			case self::MEDIA_RULE:
				// http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSMediaRule
				// http://dev.w3.org/csswg/cssom/#the-cssmediarule-interface
				// http://dev.w3.org/csswg/cssom-1/#the-cssmediarule-interface

				$this->type = self::MEDIA_RULE;
				$this->media = new Media($value);
				break;
			case self::FONT_FACE_RULE:
				// http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSFontFaceRule
				// http://www.w3.org/TR/css3-fonts/#om-fontface
				// http://dev.w3.org/csswg/css-fonts-3/#dom-cssfontfacerule

				$this->type = self::FONT_FACE_RULE;
				break;
			case self::PAGE_RULE:
				// http://www.w3.org/TR/DOM-Level-2-Style/css.html#CSS-CSSPageRule
				// http://dev.w3.org/csswg/cssom/#the-csspagerule-interface
				// http://dev.w3.org/csswg/cssom-1/#the-csspagerule-interface

				$this->type = self::PAGE_RULE;
				$this->selectorText = $value;
				break;
			case self::KEYFRAMES_RULE:
				// http://www.w3.org/TR/css3-animations/#CSSKeyframesRule-interface
				// http://dev.w3.org/csswg/css-animations-1/#csskeyframesrule

				$this->type = self::KEYFRAMES_RULE;
				$this->name = $value;
				break;
			case self::KEYFRAME_RULE:
				// http://www.w3.org/TR/css3-animations/#CSSKeyframeRule-interface
				// http://dev.w3.org/csswg/css-animations-1/#csskeyframerule

				$this->type = self::KEYFRAME_RULE;
				$this->keyText = $value;
				break;
			case self::MARGIN_RULE:
				// http://www.w3.org/TR/cssom/#the-cssmarginrule-interface
				// http://dev.w3.org/csswg/cssom/#cssmarginrule
				// http://dev.w3.org/csswg/cssom-1/#cssmarginrule

				$this->type = self::PAGE_RULE;
				$this->name = $value;
				break;
			case self::NAMESPACE_RULE:
				// http://www.w3.org/TR/cssom/#the-cssnamespacerule-interface
				// http://dev.w3.org/csswg/cssom/#the-cssnamespacerule-interface
				// http://dev.w3.org/csswg/cssom-1/#cssnamespacerule

				$this->type = self::NAMESPACE_RULE;
				$this->namespaceURI = $value;
				$this->prefix = '';
				break;
			case self::COUNTER_STYLE_RULE;
				// http://dev.w3.org/csswg/css-counter-styles/#the-csscounterstylerule-interface

				$this->name = $value;
				break;
			case self::SUPPORTS_RULE:
				// http://dev.w3.org/csswg/css-conditional-3/#at-supports
				// http://dev.w3.org/csswg/css-conditional-3/#cssconditionrule

				$this->type = self::SUPPORTS_RULE;
				$this->conditionText = new Supports($value);
				break;
			case self::DOCUMENT_RULE:
				// http://www.w3.org/TR/2012/WD-css3-conditional-20120911/#cssdocumentrule

				$this->type = self::DOCUMENT_RULE;
				$this->conditionText = new Document($value);
				break;
			case self::FONT_FEATURE_VALUES_RULE:
				// http://www.w3.org/TR/css3-fonts/#cssfontfeaturevaluesrule

				$this->type = self::FONT_FEATURE_VALUES_RULE;
				$this->fontFamily = $value;
				break;
			case self::VIEWPORT_RULE:
				// http://dev.w3.org/csswg/css-device-adapt/#cssviewportrule

				$this->type = self::VIEWPORT_RULE;
				break;
			case self::CUSTOM_MEDIA_RULE:
				// http://www.w3.org/TR/mediaqueries-4/#dom-csscustommediarule

				$this->type = self::CUSTOM_MEDIA_RULE;
				$this->name = $value;
				$this->media = new Media;
				break;
			case self::COMMENT_RULE:
				// 注释

				$this->type = self::COMMENT_RULE;
				$this->comment = $value;
				break;
			case self::PROPERTY_RULE:
				// 属性

				$this->type = self::PROPERTY_RULE;
				if (is_array($value)) {
					$string = '';
					foreach ($value as $v) {
						if ($v === NULL || $v === false) {
							continue;
						}
						$string .= $string ? (strpos($string, ':') === false ? ':' : ' ') . $v : $v;
					}
					$value = $string;
				}
				$value = explode(':', $value, 2);
				$this->privatePrefix = self::privatePrefix($value[0], true);
				$this->name = strtolower(trim($value[0]));

				// important 优先级
				if (isset($value[1])) {
					$value[1] = trim($value[1]);
					if (strcasecmp(substr($value[1], -10, 10), '!important') === 0) {
						$value[1] = trim(substr($value[1], 0, -10));
						$this->important = true;
					}
					$this->value = $value[1];
				}

				break;
			case self::FONT_FEATURE_VALUES_MAP_RULE:
				// FONT_FEATURE_VALUES_RULE 的嵌套

				$this->type = self::FONT_FEATURE_VALUES_MAP_RULE;
				$this->name = $value;
				break;
			default:
				$this->type = self::ROOT_RULE;
				if ($value !== false) {
					$this->cssText = $value;
				}
		}
	}



	public function __destruct() {
		unset($this->parentRule, $this->cssRules, $this->conditionText, $this->selectorText, $this->media);
	}



	public function __get($name) {
		switch ($name) {
			case 'type':
			case 'parentRule':
			case 'selectorText':
			case 'conditionText':
			case 'media':
				return $this->$name;
				break;
			case 'cssText':
				return $this->__toString();
				break;
			default:
		}
	}




	public function __set($name, $value) {
		switch ($name) {
			case 'type':
			case 'parentRule':
				break;
			case 'cssText':
				if ($this->parentRule) {
					if ($value !== false && $value !== '' && $value !== NULL) {
						$rules = new Rule($value);
						foreach ($rules as $rule) {
							$this->parentRule->insertRule($rule, $this);
						}
					}
					$this->parentRule->deleteRule($this);
				} elseif ($this->type === self::ROOT_RULE) {
					foreach ($this->cssRules as $rule) {
						$this->deleteRule($rule);
					}
					$this->process($value);
				}
				break;
			case 'selectorText':
				switch ($this->type) {
					case self::STYLE_RULE:
						$this->selectorText = ($value instanceof Selectors ? $value : new Selectors($value));
						break;
					case self::PAGE_RULE:
						$this->selectorText = (string) $value;
				}
				break;
			case 'conditionText':
				switch ($this->type) {
					case self::SUPPORTS_RULE:
						$this->conditionText = ($value instanceof Supports ? $value : new Supports($value));
						break;
					case self::DOCUMENT_RULE:
						$this->conditionText = ($value instanceof Document ? $value : new Document($value));
				}
				break;
			case 'media':
				switch ($this->type) {
					case self::IMPORT_RULE:
					case self::MEDIA_RULE:
					case self::CUSTOM_MEDIA_RULE:
						$this->media = ($value instanceof Media ? $value : new Media($value));
				}
				break;
			default:
				if (!isset($this->$name)) {
					$this->$name = $value;
				}
		}
	}









	/**
	 * __toString 转换成字符串
	 * @return string
	 */
	public function __toString() {
		switch ($this->type) {
			case self::COMMENT_RULE:
				// 注释
				$result = '/*'. str_replace('*/', '&ast;/', $this->comment) . '*/';
				break;
			case self::PROPERTY_RULE:
				// 属性
				$result = ($this->value = self::value($this->value)) !== false && ($this->name = self::name($this->name)) ? $this->privatePrefix . $this->name .':' .($this->format ? ' ' : ''). $this->value . ($this->important ? ' !important': '') . ';' : '';
				break;
			case self::STYLE_RULE:
				// style 样式表
				$result = $this->selectorText;
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::CHARSET_RULE;
				// 编码
				$result = '@charset "'. (self::ascii($this->encoding) ? str_replace(['"', '\\'], '', $this->encoding) : 'UTF-8') .'";';
				break;
			case self::IMPORT_RULE;
				// 引入文件
				$result = '@import url("'. self::url($this->href) .'") '. $this->media .';';
				break;
			case self::NAMESPACE_RULE:
				// 命名空间
				$result = '@'. $this->privatePrefix .'namespace '. ($this->prefix ? self::name($this->prefix, false) . ' ' : '') .'url("'. self::url($this->namespaceURI) .'");';
				break;
			case self::FONT_FACE_RULE;
				// 字体文件
				$result = '@'. $this->privatePrefix .'font-face';
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::VIEWPORT_RULE;
				// 缩放
				$result = '@'. $this->privatePrefix .'viewport';
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::COUNTER_STYLE_RULE:
				// 计数器 li 什么的
				$result = '@'. $this->privatePrefix .'counter-style '. self::name($this->name, false);
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::KEYFRAMES_RULE:
				// 动画
				$result = '@'. $this->privatePrefix .'keyframes '. self::name($this->name, false);
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::KEYFRAME_RULE:
				// 动画 单个规则
				$keys = [];
				foreach (explode(',', $this->keyText) as $key) {
					if (($key = trim($key)) && preg_match('/^(from|to|\d+\%)$/', $key = strtolower($key))) {
						$keys[] = $key;
					}
				}
				$result = implode(', ', $keys);
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::FONT_FEATURE_VALUES_RULE:
				// 字体属性  大小什么的
				$fontFamily = trim(preg_replace('/\s+/', '', $this->fontFamily));
				if (preg_match('/[a-z]+[a-z0-9 _-]/i', $fontFamily)) {
					$result = '@'. $this->privatePrefix .'font-feature-values '. $fontFamily;
					$result .= ' {';
					foreach($this->cssRules as $rule) {
						if ($this->format) {
							$rule->format = $this->format . "\t";
						}
						$result .= $rule;
					}
					$result .= $this->format . '}';
				} else {
					$result = '';
				}
				break;
			case self::FONT_FEATURE_VALUES_MAP_RULE:
				// 字体属性单条规则
				if (isset(self::$fontFeatureValuesNames[$this->name])) {
					$result = '@'. $this->name;
					$result .= ' {';
					foreach($this->cssRules as $rule) {
						if ($this->format) {
							$rule->format = $this->format . "\t";
						}
						$result .= $rule;
					}
					$result .= $this->format . '}';
				} else {
					$result = '';
				}
				break;
			case self::PAGE_RULE:
				// page 打印文档
				$result = '';
				break;
			case self::MEDIA_RULE:
				// media 分辨率控制
				$result = '@'. $this->privatePrefix .'media '. $this->media;
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::DOCUMENT_RULE:
				// 文档
				$result = '@'. $this->privatePrefix .'document ' . $this->conditionText;
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::SUPPORTS_RULE:
				// 属性支持
				$result = '@'. $this->privatePrefix .'supports ' . $this->conditionText;
				$result .= ' {';
				foreach($this->cssRules as $rule) {
					if ($this->format) {
						$rule->format = $this->format . "\t";
					}
					$result .= $rule;
				}
				$result .= $this->format . '}';
				break;
			case self::CUSTOM_MEDIA_RULE:
				// 自定义 media
				$result = ($name = self::name($this->name, true)) ? '@custom-media ' . $name .' '. $this->media : '';
				break;
			default:
				$result = '';
				foreach($this->cssRules as $rule) {
					$rule->format = $this->format;
					$result .= $rule;
				}
		}
		$result = $result && $this->format && $this->parentRule && ($this->format !== "\n" || reset($this->parentRule->cssRules) !== $this) ? $this->format . $result : $result;
		$this->format = '';
		return $result;
	}




	/**
	 * prepare 预解析
	 * @param  Rule   $rule
	 */
	protected function prepare($rule) {
		static $nesting = 0;
		// 限制嵌套层次
		if ($nesting >= self::NESTING) {
			return;
		}


		while (($char = $this->search('@;{}', $rule)) !== false) {
			switch ($char) {
				case ';':
					// 直接结束的无效值
					$this->buffer = '';
					break;
				case '}':
					// 结束括号
					$this->buffer = '';
					// 有父级 跳出
					if ($rule->parentRule) {
						break 2;
					}
					break;
				case '{':
					// 直接元素绑定
					$rule->insertRule($rule2 = new Rule($this->buffer, self::STYLE_RULE));
					// 解析属性
					$this->_propertys($rule2);
					break;
				case '@':
					// AT 规则


					// 清空缓冲区
					$this->buffer = '';

					// 读下一个
					$char = $this->search(" \t\n\r\0\x0B{}:;");

					switch ($char) {
						case ';':
						case '}':
							// 直接结束的 无效值
							break;
						default:
							// 删除前缀和名字
							unset($name, $privatePrefix);

							// 读取 AT 名
							$name = $this->buffer;


							// AT 解析前缀
							$privatePrefix = self::privatePrefix($name);


							// 清空缓冲区
							$this->buffer = '';

								// 匹配at规则
							switch ($name) {
								case 'charset':
									// 编码
									if ($this->search(';', $rule) && !$rule->parentRule && ($charset = trim($this->buffer, " \t\n\r\0\x0B\"'"))) {
										$rule->insertRule(new Rule($charset, self::CHARSET_RULE));
									}
									break;
								case 'import':
									// 引入文件
									$this->search(';', $rule);
									if (preg_match('/\s*url\(("|\')?(.+?)(?(1)\1|)\)(?:\s+(.+))?/i', $this->buffer, $matches) || preg_match('/\s*("|\')(.+?)(?(1)\1|)(?:\s+(.+))?/i', $this->buffer, $matches)) {
										$rule->insertRule($rule2 = new Rule($matches[2], self::IMPORT_RULE));
										if (!empty($matches[3])) {
											$rule2->media = $matches[3];
										}
									}
									break;
								case 'namespace':
									//  命名空间
									$this->search(';', $rule);
									if (preg_match('/\s*(?:([a-z]+[0-9a-z]*)\s+)?\s*(url\s*\()?(["\'])?(https?\:\/\/[0-9a-z\/._-])(?(3)\3|)(?(2)\)|)/i')) {
										$rule->insertRule($rule2 = new Rule($matches[4], self::NAMESPACE_RULE));
										if (!empty($matches[1])) {
											$rule2->prefix = $matches[1];
										}
										$rule2->privatePrefix = $privatePrefix;
									}
									break;
								case 'font-face':
									// 字体
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule('', self::FONT_FACE_RULE));
									$rule2->privatePrefix = $privatePrefix;
									$this->_propertys($rule2, self::$fontFacePropertys);
									break;
								case 'viewport':
									// viewport
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule('', self::VIEWPORT_RULE));
									$rule2->privatePrefix = $privatePrefix;
									$this->_propertys($rule2, self::$viewportPropertys);
									break;
								case 'counter-style':
									// 有序规则 计数器定义
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule(trim($this->buffer), self::COUNTER_STYLE_RULE));
									$rule2->privatePrefix = $privatePrefix;
									$this->_propertys($rule2,self::$counterStylePropertys);
									break;
								case 'keyframes':
									// 动画
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule(trim($this->buffer), self::KEYFRAMES_RULE));
									$rule2->privatePrefix = $privatePrefix;
									$this->buffer = '';

									// 循环遍历
									while ($this->search('{}', $rule2) === '{') {
										$rule2->insertRule($rule3 = new Rule($this->buffer, self::KEYFRAME_RULE));
										$this->_propertys($rule3);
										$this->buffer = '';
									}
									break;
								case 'font-feature-values':
									// 字体盒子  创建自定义字体
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule(trim($this->buffer), self::FONT_FEATURE_VALUES_RULE));
									$rule2->privatePrefix = $privatePrefix;
									$this->buffer = '';

									// 循环遍历
									while ($this->search('{}', $this) === '{') {
										$rule2->insertRule($rule3 = new Rule($this->buffer, self::FONT_FEATURE_VALUES_MAP_RULE));
										$this->_propertys($rule3, self::$fontFeatureValuesNames);
										$this->buffer = '';
									}
									break;
								case 'page':
									// page 暂时不支持
									if ($char !== '{') {
										$char = $this->search(';{}');
									}
									if ($char !== ';') {
										// 循环同样的 {} 嵌套
										$i = 0;
										do {
											if ($char === '{') {
												++$i;
											} else {
												--$i;
											}
										} while ($i > 0 && ($char = $this->search('{}')));
									}
									break;
								case 'media':
								case 'supports':
								case 'document':
									// meta supports document 规则
									static $types = ['media' => self::MEDIA_RULE, 'supports' => self::SUPPORTS_RULE, 'document' => self::DOCUMENT_RULE];
									$char !== '{' && $this->search('{', $rule);
									$rule->insertRule($rule2 = new Rule($this->buffer,  $types[$name]));
									$rule2->privatePrefix = $privatePrefix;
									$this->buffer = '';
									++$nesting;
									$this->prepare($rule2);
									--$nesting;
									break;
								case 'custom-media':
									// 编码
									if ($this->search(';', $rule) && count($buffer = preg_split('/\s+/', $this->buffer, 2, PREG_SPLIT_NO_EMPTY)) === 2) {
										$rule->insertRule($rule2 = new Rule($buffer[0], self::CUSTOM_MEDIA_RULE));
										$rule2->media = $buffer[1];
									}
									break;
								default:
									// 不明属性
									if ($char !== '{') {
										$char = $this->search(';{}');
									}
									if ($char !== ';') {
										// 循环同样的 {} 嵌套
										$i = 0;
										do {
											if ($char === '{') {
												++$i;
											} else {
												--$i;
											}
										} while ($i > 0 && ($char = $this->search('{}')));
									}
							}
							$this->buffer = '';
					}
			}
		}
	}

















	/**
	 * _propertys 解析属性
	 * @param  StyleRule $rule
	 * @param  array     $inArray
	 */
	private function _propertys(Rule $rule, array $inArray = []) {
		// 清空缓冲区
		$this->buffer = '';

		$propertys = [];
		while (($char = $this->search('};', $rule)) !== false) {
			switch ($char) {
				case '}':
					break 2;
				case ';':
					$propertys[] = $this->buffer;
					$this->buffer = '';
					break;
			}
		}

		if ($this->buffer) {
			$propertys[] = $this->buffer;
			$this->buffer = '';
		}

		// 发布
		foreach ($propertys as $value) {
			if (!($value = trim($value)) || count($value = explode(':', $value, 2)) !== 2) {
				continue;
			}

			$value[0] = trim($value[0]);

			// 指定属性
			if ($inArray && !in_array($value[0] = strtolower(trim($value[0])), $inArray, true)) {
				continue;
			}

			$rule->insertRule(new Rule($value, self::PROPERTY_RULE));
		}
		$this->buffer = '';
	}





	/**
	 * format 格式化
	 * @param  boolean $format 是否格式化
	 * @return this
	 */
	public function format($format) {
		$this->format = $format ? "\n" : '';
		return $this;
	}





	public function insertRule(Rule $rule, $index = NULL) {
		if ($index === NULL) {
			// 最后
			$rule->parentRule && $rule->parentRule->deleteRule($rule);
			$rule->parentRule = $this;
			$this->cssRules[] = $rule;
		} elseif ($index instanceof Rule) {
			// 某个元素之前
			$cssRules = [];
			foreach ($this->cssRules as $value) {
				if ($value === $index) {
					$rule->parentRule && $rule->parentRule->deleteRule($rule);
					$rule->parentRule = $this;
					$cssRules[] = $rule;
				}
				$cssRules[] = $value;
			}
			$this->cssRules = $cssRules;
		} else {
			// 偏移 位置
			$rule->parentRule && $rule->parentRule->deleteRule($rule);
			$rule->parentRule = $this;
			array_splice($this->cssRules, $index, 0,[$rule]);
		}
		return $rule;
	}

  	public function deleteRule(Rule $rule) {
  		if (($index = array_search($rule, $this->cssRules, true)) !== false) {
			unset($this->cssRules[$index]);
			$this->cssRules = array_values($this->cssRules);
			$rule->parentRule = NULL;
		}
		return $rule;
  	}

	/**
	 * replace 替换一条规则
	 * @param  Rule   $new 新的规则
	 * @param  Rule   $old 旧的规则
	 * @return Rule|boolean  成功返回旧的规则 否则 false
	 */
	public function replaceRule(Rule $new, Rule $old) {
		if (($index = array_search($old, $this->cssRules, true)) !== false) {
			$new->parentRule && $new->parentRule->deleteRule($new);
			$this->cssRules[$index] = $new;
			$new->parentRule = $this;

			$old->parentRule = NULL;
			return $old;
		}
		return false;
	}




	public function offsetSet($name, $value) {
		if ($value === NULL || $value === false) {
			if ($name !== NULL) {
				unset($this->cssRules[$name]);
			}
		} elseif (isset($this->cssRules[$name])) {
			if ($value instanceof Rule) {
				$this->replaceRule($value, $this->cssRules[$name]);
			}
		} elseif ($value instanceof Rule) {
			$this->insertRule($value);
		}
	}

	public function offsetExists($name) {
		return isset($this->cssRules[$name]);
	}

	public function offsetUnset($name) {
		unset($this->cssRules[$name]);
	}

	public function offsetGet($name) {
		return isset($this->cssRules[$name]) ? $this->cssRules[$name] : NULL;
	}

	public function getIterator() {
		return new ArrayIterator($this->cssRules);
	}

	public function count() {
		return count($this->cssRules);
	}
}