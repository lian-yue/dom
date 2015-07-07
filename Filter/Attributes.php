<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-01 10:31:52
/*	Updated: UTC 2015-06-29 11:52:20
/*
/* ************************************************************************** */
namespace Loli\DOM\Filter;
use Loli\DOM\CSS\Media, Loli\DOM\Node;
class Attributes {
	// url 允许的协议
	protected $schemes = ['http', 'https', 'ftp', 'gopher', 'news', 'telnet', 'rtsp', 'mms', 'callto', 'bctp', 'synacast', 'thunder', 'flashget', 'qqid', 'magnet', 'ed2k'];

	// target 允许的值
	protected $targets = ['_blank'];

	// 允许的类型
	protected $types = ['text', 'hidden', 'file', 'password', 'email', 'url', 'search', 'number', 'color', 'range', 'tel', 'datetime-local', 'image', 'datetime', 'date', 'month', 'week', 'time', 'submit', 'reset', 'button', 'textarea', 'select', 'radio', 'checkbox', 'application/x-shockwave-flash', 'text/plain'];


	// 允许匹配的 id  数组
	protected $id = [];

	// 允许匹配的 class 数组
	protected $class = [];

	// 允许的 rel
	protected $rels = [];


	// name 允许的值
	protected $name = [];

	// 允许匹配的 class name id 前缀
	protected $prefix;

	// classid 允许的值 ie 控件的
	protected $classID = ['clsid:d27cdb6e-ae6d-11cf-96b8-444553540000'];

	// 过滤回调函数
	protected $filters = [
		'src' => 'url',
		'data' => 'url',
		'cite' => 'url',
		'action' => 'url',
		'movie' => 'url',
		'base' => 'url',
		'href' => 'url',

		'span' => 'intval',
		'rows' => 'intval',
		'cols' => 'intval',
		'size' => 'intval',
		'border' => 'intval',
		'colspan' => 'intval',
		'rowspan' => 'intval',
		'maxlength' => 'intval',
		'cellpadding' => 'intval',
		'cellspacing' => 'intval',
		'frameborder' => 'intval',
		'tabIndex' => 'intval',


		'value' => 'value',
		'title' => 'value',
		'label' => 'value',
		'alt' => 'value',
		'vars' => 'value',
		'flashvars' => 'value',
		'themenu' => 'value',
		'salign' => 'value',
		'allowfullscreen' => 'value',
		'contenteditable' => 'value',
		'spellcheck' => 'value',
		'defaultvalue' => 'value',
		'pattern' => 'value',
		'placeholder' => 'value',

		'min' => 'minMaxStep',
		'max' => 'minMaxStep',
		'step' => 'minMaxStep',


		'scoped' => 'one',
		'controls' => 'one',
		'autoplay' => 'one',
		'checked' => 'one',
		'disabled' => 'one',
		'readonly' => 'one',
		'required' => 'one',
		'autofocus' => 'one',
		'async' => 'one',
		'defer' => 'one',
		'default' => 'one',


		'id' => 'id',
		'for' => 'id',
		'list' => 'id',
		'form' => 'id',

		'lang' => 'lang',
		'srclang' => 'lang',

		'width' => 'widthHeight',
		'height' => 'widthHeight',


		'media' => 'media',
		'style' => 'style',
		'class' => 'class_',

		'rel' => 'rel',
		'name' => 'name',
		'type' => 'type',
		'target' => 'target',
		'datetime' => 'datetime',
		'dir' => 'dir',
		'usemap' => 'usemap',
		'shape' => 'shape',
		'coords' => 'coords',
		'method' => 'method',
		'valign' => 'valign',
		'wmode' => 'wmode',
		'quality' => 'quality',
		'scale' => 'scale',
		'autocomplete' => 'autoComplete',
		'classid' => 'classID',
		'kind' => 'kind',
	];




	// 当前标签名
	protected $tagName;


	public function __construct(Styles $style = NULL) {
		$this->style = $style;
	}

	public function __invoke() {
		return call_user_func_array([$this, 'filters'], func_get_args());
	}

	public function filters(Node $nodes) {
		foreach ($nodes->all() as $node) {
			if ($node->nodeType === Node::ELEMENT_NODE) {
				$this->tagName = $node->tagName;
				if (strcasecmp($this->tagName, 'param') === 0) {
					// 变量
					$name = $node->attributes['name'];
					$value = $node->attributes['value'];
					$value = $this->filter($name, $value);

					// 删除所有属性
					foreach ($node->attributes[$name] as $attributeName => $attributeValue) {
						unset($node->attributes[$attributeName]);
					}

					// 设置name 和 value 属性
					if ($value !== false && $value !== NULL) {
						$node->attributes['name'] = $name;
						$node->attributes['value'] = $value;
					}
				} else {
					foreach ($node->attributes as $name => $value) {
						$node->attributes[$name] = $this->filter($name, $value);
					}
				}
			}
		}
	}

	/**
	 * filter 过滤
	 * @param  string $name
	 * @param  string $value
	 * @return
	 */
	protected function filter($name, $value) {
		if (empty($this->filters[$name])) {
			if (substr($name, 0, 5) === 'data-') {
				$name = substr($name, 5);
			}
			if (empty($this->filters[$name])) {
				return NULL;
			}
		}
		return call_user_func([$this, $this->filters[$name]], $value);
	}

	/**
	 * one  true 的
	 * @return string
	 */
	protected function one() {
		return '1';
	}

	/**
	 * value 直接返回 值
	 * @param  string $value
	 * @return string
	 */
	protected function value($value) {
		return $value;
	}

	/**
	 * intval 数字的
	 * @param  string|integer $value
	 * @return integer
	 */
	protected function intval($value) {
		return (int) $value;
	}

	/**
	 * url url 地址
	 * @param  [type] $value [description]
	 * @return string|null
	 */
	protected function url($value) {
		if (!$value || !($parse = parse_url($value))) {
			return NULL;
		}
		if (!empty($parse['scheme']) && !in_array(strtolower($parse['scheme']), $this->schemes)) {
			return NULL;
		}
		return $value;
	}

	/**
	 * class_ class名
	 * @param  string $value [description]
	 * @return string|null
	 */
	protected function class_($value) {
		if (!$value = trim($value)) {
			return NULL;
		}
		$results = [];
		foreach (explode(' ', trim($value)) as $class) {
			if (!($class = trim($class)) || (!in_array($class, $this->class, true) && $this->prefix && substr($class, 0, strlen($this->prefix)) !== $this->prefix) || !preg_match('/^[0-9a-z_-]+$/i', $class)) {
				continue;
			}
			$results[] = $class;
		}
		return $results ? implode(' ', $results) : NULL;
	}

	/**
	 * id id名
	 * @param  string $value [description]
	 * @return string|null
	 */
	protected function id($value) {
		if (!$value) {
			return NULL;
		}
		if (in_array($value, $this->id, true)) {
			return $value;
		}
		if ($this->prefix && substr($value, 0, strlen($this->prefix)) !== $this->prefix) {
			return NULL;
		}
		if (!preg_match('/^[0-9a-z_-]+$/i', $value)) {
			return NULL;
		}
		return $value;
	}

	/**
	 * name
	 * @param  string $value
	 * @return string|null
	 */
	protected function name($value) {
		if (!$value) {
			return NULL;
		}
		if (in_array($value, $this->name, true)) {
			return $value;
		}
		if ($this->prefix && substr($value, 0, strlen($this->prefix)) !== $this->prefix) {
			return NULL;
		}
		return $value;
	}



	protected function type($value) {
		// 其他允许的标签
		if (in_array($value = strtolower($value), $this->types, true)) {
			return $value;
		}

		// 视频 音频 图片
		if (preg_match('/^(video|audio|image)\/[0-9a-z_-]+$/i', $value)) {
			return $value;
		}
		return NULL;
	}

	/**
	 * target 属性
	 * @param  string $value
	 * @return string|null
	 */
	protected function target($value) {
		return in_array($value, $this->targets) ? $value : NULL;
	}


	/**
	 * datetime 属性
	 * @param  string $value
	 * @return string
	 */
	protected function datetime($value) {
		return preg_replace('/[^0-9a-z_: -]/i', '', $value);
	}


	/**
	 * lang 属性
	 * @param  string $value
	 * @return string|null
	 */
	protected function lang($value) {
		if (!preg_match('/^[a-z_-]{2,10}$/i', $value)) {
			return NULL;
		}
		return $value;
	}

	/**
	 * dir 属性规定元素内容的文本方向
	 * @param  string $value
	 * @return string|null
	 */
	protected function dir($value) {
		if (!in_array($value = strtolower($value), ['ltr', 'rtl'], true)) {
			return NULL;
		}
		return $value;
	}

	/**
	 * usemap 属性将图像定义为客户端图像映射
	 * @param  string $value
	 * @return string|null
	 */
	protected function usemap($value) {
		if (!preg_match('/^[#.]'. preg_quote($this->prefix, '/') .'[0-9a-z_-]+$/i', $value)) {
			return NULL;
		}
		return $value;
	}


	/**
	 * shape 属性用于定义图像映射中对鼠标敏感的区域的形状
	 * @param  string $value
	 * @return string|null
	 */
	protected function shape($value) {
		if (!in_array($value = strtolower($value), ['default', 'rect', 'circ', 'poly'], true)) {
			return NULL;
		}
		return $value;
	}


	/**
	 * coords 属性规定区域的 x 和 y 坐标
	 * @param  string $value
	 * @return string
	 */
	protected function coords($value) {
		return preg_replace('/[^0-9a-z,]/i', '', $value);
	}


	/**
	 * method 属性规定如何发送表单数据
	 * @param  string $value
	 * @return string|null
	 */
	protected function method($value) {
		return $value ? (strtoupper($value) == 'POST' ? 'POST' : 'GET') : NULL;
	}

	/**
	 * valign 属性规定单元格中内容的垂直排列方式
	 * @param  string $value
	 * @return string|null
	 */
	protected function valign($value) {
		if (!in_array($value = strtolower($value), ['top', 'middle', 'bottom', 'baseline'])) {
			return NULL;
		}
		return $value;
	}



	/**
	 * wmode flash 载入方式
	 * @param  string $value
	 * @return string
	 */
	protected function wmode($value) {
		return in_array($value = strtolower($value), ['transparent', 'window', 'opaque']) ? $value : 'window';
	}




	/**
	 * quality flash 动画质量
	 * @param  string $value
	 * @return string
	 */
	protected function quality($value) {
		return in_array($value = strtolower($value), ['low', 'medium', 'high', 'autolow', 'autohigh', 'best']) ? $value : 'high';
	}




	/**
	 * [scale description]
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	protected function scale($value) {
		return in_array($value = strtolower($value), ['default', 'showall', 'noborder', 'exactfit', 'noscale']) ? $value : 'default';
	}




	/**
	 * autoComplete 表单自动完成
	 * @param  string $value
	 * @return string|null
	 */
	protected function autoComplete($value) {
		return in_array(strtolower($value), ['on', 'off']) ? $value : NULL;
	}



	/**
	 * classID  ie 的插件id classid
	 * @param  string $value
	 * @return string|null
	 */
	protected function classID($value) {
		return in_array($value = strtolower($value), $this->classID, true) ? $value : NULL;
	}



	/**
	 * rel 属性规定当前文档与被链接文档之间的关系
	 * @param  string $value
	 * @return string|null
	 */
	protected function rel($value) {
		return in_array($value = strtolower($value), $this->rels, true) ? $value : NULL;
	}


	/**
	 * min max step 属性
	 * @param  string $value
	 * @return string
	 */
	protected function minMaxStep($value) {
		return preg_replace('/^[^0-9a-z_:% -]$/i', '', $value);
	}


	/**
	 * width height
	 * @param  string $value
	 * @return string
	 */
	protected function widthHeight($value) {
		return preg_replace('/[^0-9.%]/i', '', $value);
	}



	protected function style($value) {
		return $value && $this->style ? $this->style->values($value) : NULL;
	}


	/**
	 * media 属性
	 * @param  string $value
	 * @return string|null
	 */
	protected function media($value) {
		if (strcasecmp($this->tagName, 'style') !== 0 || !$this->style) {
			return NULL;
		}
		return new Media($value);
	}
	/**
	 * kind 属性规定轨道的种类
	 * @param  [type] $value [description]
	 * @return [type]        [description]
	 */
	protected function kind($value) {
		return  in_array($value = strtolower($value), ['captions', 'chapters', 'descriptions', 'metadata', 'subtitles'], true) ? $value : NULL;
	}


}