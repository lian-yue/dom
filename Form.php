<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2014-03-24 11:06:22
/*	Updated: UTC 2015-04-14 03:08:32
/*
/* ************************************************************************** */
namespace Loli\HTML;
class Form {


	private static $_tags = ['span', 'div', 'p', 'a', 'code'];
	private static $_types = ['text', 'hidden', 'file', 'password', 'email', 'url', 'search', 'number', 'color', 'range', 'tel', 'datetime-local', 'image', 'datetime', 'date', 'month', 'week', 'time', 'submit', 'reset', 'button', 'textarea', 'select', 'radio', 'checkbox', 'fieldset', 'table', 'lists', 'menu'];

	/**
	 * $_results
	 * @var array
	 */
	private $_results = [];


	public function __construct(array $array = []) {
		$array && call_user_func_array([$this, '__invoke'], func_get_args());
	}

}

/*class Form{

	private static $_tags = ['span', 'div', 'p', 'a', 'code'];
	private static $_types = ['text', 'hidden', 'file', 'password', 'email', 'url', 'search', 'number', 'color', 'range', 'tel', 'datetime-local', 'image', 'datetime', 'date', 'month', 'week', 'time', 'submit', 'reset', 'button', 'textarea', 'select', 'radio', 'checkbox', 'fieldset', 'table', 'lists', 'menu'];

	/**
	 * $_results
	 * @var array
	 *//*
	private $_results = [];


	public function __construct(array $array = []) {
		$array && call_user_func_array([$this, '__invoke'], func_get_args());
	}


	/**
	 * __toString 输出
	 *//*
	public function __toString() {
		return implode('', $this->_forms);
	}


	/**
	 * results 输出
	 *//*
	public function results() {
		return $this->_results;
	}

	private static function _parse(array $array) {
		extract($array, EXTR_SKIP);
		$type = empty($type) ? reset(self::$_types) : strtr($type, '_', '-');
		if (!in_array($type, self::$_types)) {
			throw new Exception('Form type unknown');
		}

		switch ($type) {
			case 'lists':
				// class  名
				$class = empty($class) ? [] : (array) $class;
				$class[] = 'form-lists';


				// 查询数组
				$query = empty($query) ? [] : parse_string($query);
				if (isset($query['order']) && !is_array($query['order'])) {
					unset($query['order']);
				}

				// 需要排序的信息
				$order = empty($order) ? [] : (array) $order;


				// 表头
				$thead = empty($thead) ? (empty($head) ? [] : $head) : $thead;
				$thead = array_unnull($thead);

				// 表内容
				$tbody = empty($tbody) ? (empty($body) ? (empty($value) ? [] : $value) : $body) : $tbody;

				// 表
				$keys = array_keys($thead ? $thead : reset($tbody));


				foreach ($tbody as $key => &$value) {
					$value = (array) $value;
					foreach ($keys as $k) {
						$value[$k] = isset($value[$k]) ? (is_array($value[$k]) ? self::_parse(empty($value[$k]['type']) ? 'menu' : $value[$k]['type'], $value[$k]) : $value[$k]) : '';
					}
				}
				unset($value);
				$array = compact('type', 'class', 'query', 'order', 'thead', 'tbody');
				break;
			case 'table':
				// table 表单
				$class = empty($class) ? [] : (array) $class;
				$class[] = 'form-forms';
				$value = empty($value) ? (empty($tbody) ? [] : $tbody) : $value;
				foreach ($value as $key => &$quote) {
					$quote = is_string($quote) ? ['value' => $quote, 'class' => []] : $quote + ['class' => []];
					$quote['class'] = (array) $quote['class'];
					$quote['class'][] = $i % 2 ? 'odd' : 'even';
					$value['class'][] ='tr-'. $key;
					if (isset($value['value']) && is_array($value['value'])) {
						$value['value'] = self::_parse(empty($value['value']['type']) ? 'menu' : $value['value']['type'], $value['value']);
					}
				}
				unset($quote);
				$array = compact('type', 'class', 'value');
				break;
			case 'menu':
				// 导航
				$class = empty($class) ? [] : (array) $class;
				$class[] = 'form-menu';
				$value = empty($value) ? [] : (array) $value;
				foreach ($value as $key => &$quote) {
					if (!is_array($quote)) {
						$quote = ['name' => $quote];
					}
				}
				unset($quote);
				$array = compact('type', 'class', 'value');
				break;
			case 'group':
			case 'fieldset':
				$type = 'form-fieldset';
				$class = empty($class) ? [] : (array) $class;
				$class[] = 'form-fieldset';
				$title = empty($title) ? (empty($legend) ? 'Ungrouped' : $legend) : $title;
				$value = empty($value) ? '' : (is_array($value) ? self::_parse($value, false) : $value);
				$array = compact('type', 'class', 'title', 'value');
				break;
			default:
				$name = empty($name) ? 'form' : $name;
				$kid = 'form-' . preg_replace('/[^0-9a-z_-]/i','_', $name);
				$id = empty($id) ? $kid : $id;
				$class = empty($class) ? [$kid] : (array) $class;
				$option = empty($option) ? [] : (array) $option;
				$in = in_array($type, ['checkbox', 'radio']) && !empty($option);

		}



	}

	/**
	 * __call 回调函数
	 * @param  string $name 表单类型名
	 * @param  array $args  回调的参数
	 * @return this
	 *//*
	public function __call($name, $args) {
		$name = strtr($name, '_', '-');
		if (!in_array($name, self::$_types)) {
			throw new Exception('Form type unknown');
		}
		$array = empty($args[0]) ? [] : (array) $args[0];
		unset($args);
		extract($array, EXTR_SKIP);
		switch ($name) {
			case 'lists':
				// class  名
				$class = empty($class) ? [] : (array) $class;
				$class[] = 'form-lists';


				// 查询数组
				$query = empty($query) ? [] : parse_string($query);
				if (isset($query['order']) && !is_array($query['order'])) {
					unset($query['order']);
				}

				// 需要排序的信息
				$order = empty($order) ? [] : (array) $order;

				// 表头
				$thead = empty($thead) ? (empty($head) ? [] : $head) : $thead;
				$thead = array_unnull($thead);

				// 表内容
				$tbody = empty($tbody) ? (empty($body) ? [] : $body) : $tbody;

				foreach ($tbody as $key => $value) {

				}
				break;
			default:
				# code...
				break;
		}



		/*if (in_array($name, ['lists', 'table', 'menu', 'fieldset'])) {
			$result = '';
		} else {
			$array['name'] = empty($array['name']) ? 'form' : $array['name'];
			$id = 'form-' . preg_replace('/[^0-9a-z_-]/i','_', $array['name']);
			$array += ['class' => [], 'id' => $id, 'value' => ''];
			$array['type'] = $name;
			$array['option'] = empty($array['option']) ? [] : (array) $array['option'];
			$in = in_array($array['type'], ['checkbox', 'radio']) && !empty($array['option']);


			if($in) {
				$class = [];
				foreach ($array['option'] as $key => $value) {
					$class[$key][] = $name;
					if (is_array($array['class']) && !empty($array['class'][$key])) {
						$class[$key][] = $array['class'][$key];
					} else {
						$class[$key][] = is_array($array['class']) || empty($array['class']) ? $id : preg_replace('/[^0-9a-zA-Z_ -]/','', $array['class']);
					}
				}
				$array['class'] = $class;
			} else {
				$array['class'] = $array['class'] ? (array) $array['class'] : [$id];
				$array['class'][] = $name;
			}




			if ($in) {
				$disabled = [];
				foreach ($array['option'] as $key => $value) {
					$disabled[$key] = !empty($array['disabled']) && (!is_array($array['disabled']) || in_array((string) $key, $array['disabled'])) ? 'disabled' : '';
					if ($disabled[$key]) {
						$array['class'][$key][] = 'disabled';
					}
				}
				$array['disabled'] = $disabled;
			} elseif (!empty($array['disabled'])) {
				$array['disabled'] = 'disabled';
				$array['class'][] = 'disabled';
			}


			if ($in) {
				$readonly = [];
				foreach ($array['option'] as $key => $value) {
					$readonly[$key] =! empty($array['readonly']) && (!is_array($array['readonly']) || in_array((string) $key, $array['readonly'])) ? 'readonly' : '';
					if ($readonly[$key]) {
						$array['class'][$key][] = 'readonly';
					}
				}
				$array['readonly'] = $readonly;
			} elseif (!empty($array['readonly'])) {
				$array['readonly'] = 'readonly';
				$array['class'][] = 'readonly';
			}


			if ($array['type'] == 'select' && !empty($array['multiple'])) {
				$array['class'][] = 'multiple';
			}

			// 值是数组
			if (in_array($array['type'], ['checkbox', 'select'])) {
				$array['value'] = (array) $array['value'];
			} elseif (is_array($array['value']) || is_object($array['value'])) {
				$array['value'] = htmlspecialchars(json_encode($array['value']), ENT_QUOTES);
			}

			// html 转义
			self::_text($array);
			$result = empty($array['label']) ? '' : '<label for="'. $array['id'] . '" class="label-'. $array['id'] . ' form-label">'. $array['label'] . '</label>';
		}








		/*switch ($name) {
			case 'lists':
				// class  名
				$class = empty($array['class']) ? [] : (array) $array['class'];
				$class[] = 'form-lists';


				// 查询数组
				$query = empty($array['query']) ? [] : parse_string($array['query']);
				if (isset($query['order']) && !is_array($query['order'])) {
					unset($query['order']);
				}

				// 需要排序的信息
				$order = empty($array['order']) ? [] : (array) $array['order'];


				// 表头
				$thead = empty($array['thead']) ? (empty($array['head']) ? [] : $array['head']) : $array['thead'];
				$thead = array_unnull($thead);


				// 表内容
				$tbody = empty($array['tbody']) ? (empty($array['body']) ? [] : $array['body']) : $array['tbody'];






				$result .= '<table class="'. implode(' ', $class) .'" >';

				// 表头
				if ($thead) {
					$result .= '<thead>';
					$result .= '<tr>';
					foreach ($thead as $key => $value) {
						if (in_array($key, $order)) {
							$desc = isset($query['order'][$key]) && ((is_string($query['order'][$key]) && strtoupper($query['order'][$key]) === 'DESC') || $query['order'][$key] < 0);
							$href = $query;
							unset($href['order'][$key]);
							$href['order'][$key] = $desc ? -1 : 1;
							$result .= '<td class=" td-'. $key .' '. ($desc ? 'desc' : 'asc') .'"><a href="?'. merge_string($href) .'"><span>' . $value . '</span><sorting></sorting></a></td>';
						} else {
							$result .= '<td class="td-'. $key .'"><span>' .$value. '</span></td>';
						}
					}
				}

				// 表
				$keys = array_keys($thead ? $thead : reset($tbody));

				$i = 0;
				foreach ($tbody as $key => $value) {
					$class = [$i % 2 ? 'odd' : 'even', 'tr-i-' . $i, 'tr-' . $key];
					$result .= '<tr class="' .implode(' ', $class). '" >';
					foreach ($keys as $k) {
						$v = isset($value[$k]) ? $value[$k] : '';
						if (is_array($v)) {
							$v = self::get(is_int(key($v)) ? ['type' => 'menu', 'value' => $v] : $v);
						}
						$result .= '<td class="td-'. $k .'">' . $v . '</td>';
					}
					$result .= '</tr>';


					++$i;
				}

				break;
			case 'table':
				// table 表单
				$class = empty($array['class']) ? [] : (array) $array['class'];
				$class[] = 'form-table';
				$result .= '<table class="' . implode(' ', $class) .'" >';
				$result .= '<tbody>';
				$i = 0;
				foreach (empty($array['value']) ? (empty($array['tbody']) ? [] : $array['tbody']) : $array['value'] as $key => $value) {
					$value = is_string($value) ? ['value' => $value, 'class' => []] : $value + ['class' => []];

					if (isset($value['value']) && is_array($value['value'])) {
						$value['value'] = self::get(is_int(key($value['value'])) ? ['type' => 'menu', 'value' => $value['value']] : $value['value'], false);
					}
					$value['class'] = (array) $value['class'];
					$value['class'][] = $i % 2 ? 'odd' : 'even';
					$value['class'][] ='tr-i-'. $i;
					$value['class'][] ='tr-'. $key;

					if (isset($value['title']) || isset($value['value'])) {
						$result .= '<tr class="'. implode(' ', $value['class']) .'">';
						if (isset($value['title'])) {
							$result .= '<th class="title" ' . (isset($value['value']) ? '' : 'colspan="2"') . '>' .$value['title']. '</th>';
						}
						if (isset($value['value'])) {
							$result .= '<td class="value" ' . (isset($value['title']) ? '' : 'colspan="2"') . '>' .$value['value']. '</td>';
						}
						$result .= '</tr>';
					}
					++$i;
				}
				$result .= '</tbody>';
				$result .= '</table>';
				break;
			case 'menu':
				// 导航
				$class = empty($array['class']) ? [] : (array) $array['class'];
				$class[] = 'form-menu';
				$result .= '<ul class="' . implode(' ', $class) .'" >';
				$i = 0;
				foreach (empty($array['value']) ? [] : (array) $array['value'] as $key => $value) {
					$result .= '<li class="li-'. $key .'">' . $value .'</li>';
				}
				$result .= '</ul>';
				break;
			case 'fieldset':
				// 列表
				$class = empty($array['class']) ? [] : (array) $array['class'];
				$class[] = 'form-fieldset';
				$result .= '<fieldset class="'. implode(' ', $class) .'">';
				if (isset($array['legend'])) {
					$result .='<legend class="form-legend">'. $array['legend']  .'</legend>';
				}
				if (isset($array['value'])) {
					if (is_array($array['value'])) {
						foreach ($array['value'] as $key => $value) {
							$result .='<div class="form-div form-div-'. $key .'">'. (is_array($value) ? self::get($value, false) : $value) . '</div>';
						}
					} else {
						$result .= $array['value'];
					}
				}
				$result .= '</fieldset>';
				break;
			case 'checkbox':
				foreach ($array['option'] as $key => $value) {
					$result .= '<label for="'. $array['id'] . '-' . $key .'" class="checkbox-label checkbox-'. $array['id'] .' checkbox-'. $array['id'] .'-'. $key .'">';
					$result .= '<input type="'. $array['type'] .'" name="'. $array['name'] .'" id="' . $array['id'] . '-' . $key .'" class="'. $array['class'][$key] . '" ' . $array['disabled'][$key] . $array['readonly'][$key].  ' value="'. $key .'" '.(in_array((string) $key,  $array['value']) ? 'checked="checked"' : '').' />';
					$result .= '<span class="checkbox-span form-span">' . $value . '</span>';
					$result .= '</label>';
				}
				break;
			case 'radio':
				foreach ($array['option'] as $key => $value) {
					$result .= '<label for="'. $array['id'] . '-' . $key .'" class="radio-label radio-'. $array['id'] .'  radio-'. $array['id'] .'-'. $key .'">';
					$result .= '<input type="'. $array['type'] .'" name="'. $array['name'] .'" id="' . $array['id'] . '-' . $key .'" class="'. $array['class'][$key] . '" ' . $array['disabled'][$key] . $array['readonly'][$key] . ' value="'. $key .'" '.($array['value'] == $key ? 'checked="checked"' : '').' />';
					$result .= '<span class="radio-span form-span">' . $value . '</span>';
					$result .= '</label>';
				}
				break;
			case 'select':
				$result .= '<select '. self::_attributes($array, ['value', 'option']) .' >';
				foreach ($array['option'] as $key => $value) {
					if (is_array($value) && isset($value['label']) && isset($value['value'])) {
							$result .= '<optgroup class="optgroup-'. $key .'" label="'. $value['label'] .'">';
							foreach ($value['value'] as $k => $v) {
								$result .= '<option '. (in_array($k, $array['value']) ? ' selected="selected"' : '') .' class="select-'. $k .' select-'. $key .'-'. $k .'" value="'. $k .'">'. $v .'</option>';
							}
							$result .= '</optgroup>';
					} else {
						$result .= '<option '. (in_array($key, $array['value']) ? ' selected="selected"' : '') .' class="select-'. $key .'" value="'. $key .'">'. $value .'</option>';
					}
				}
				$result .= '</select>';
				break;
			case 'textarea':
				$result .= '<textarea '. self::_attributes($array, ['value']) .' >'. $array['value'] .'</textarea>';
				break;
			case 'button':
				$array['name'] = empty($array['name']) ? 'button' : $array['name'];
				$array['type'] = 'submit';


				$result .= '<button '. self::_attributes($array, ['value']) . '><strong>'. $array['value'] .'</strong></button>';
				break;
			case 'submit':
				$array['name'] = isset($array['name']) ? 'submit' : $array['name'];
			default:
				$result .= '<input '. self::_attributes($array) . '/>';
		}


		$result .= self::_tags($array);


		$this->_results[] = $result;*/
		//return $this;
	//}

	/**
	 * __invoke 作为函数执行
	 * @param  array  $array 数组
	 * @return this
	 */
	/*public function __invoke(array $array) {
		return $this->__call(empty($array['type']) ? reset(self::$_types) : $array['type'], func_get_args());
	}






	/**
	 * __callStatic 静态回调方法
	 * @param  string $name 方法名
	 * @param  array $args 回调参数二维数组
	 * @return string or echo
	 */
	/*public static function __callStatic($name, $args) {
		$class = __CLASS__;
		return call_user_func_array([new $class, $name], $args);
	}




	/**
	 * _tags tags名
	 * @param  array  $array 数组
	 * @param  array  $tags  只能匹配的key
	 * @return string
	 */
	/*private static function _tags(array $array, array $tags = []) {
		$result = '';
		foreach (array_intersect_key($array, array_flip($tags ? $tags : self::$_tags)) as $key => $value) {
			if (!$value) {
				continue;
			}
			if (is_array($value)) {
				$this->_text($value, []);
				$result .= '<' . $key . self::_attributes($value) . '>';
			} else {
				$result .= '<' . $key .' class="'. $key .'-'. $array['id'] . ' ' . $array['type'] .'-'. $key .' form-'. $key .'">' . $value;
			}
			$result .= '</'. $key .'>';
		}
		return $result;
	}



	/**
	 * _attributes
	 * @param  array  $array 数组
	 * @param  array  $names 不使用的names
	 * @return this
	 */
	/*private static function _attributes(array $array, array $names = []) {
		$result = '';
		foreach ($array as $key => $value) {
			if ($key == 'label' || $key == 'legend' || in_array($key, self::$_tags) || in_array($key, $names) || (!$value && !in_array($key, ['value', 'min', 'max']))) {
				continue;
			}
			$value = is_array($value) || is_object($value) ? reset($value) : $value;
			$result .= ' '. $key .'="'. $value .'"';
		}
		return $result;
	}


	/**
	 * _text 过滤html 代码
	 * @param  array  &$array 数组
	 * @param  array  $tags = NULL   不过滤的标签
	 */
	/*private static function _text(array &$array, array $tags = NULL) {
		foreach ($array as $key => &$value) {
			if (in_array($key, $tags === NULL ? self::$_tags : $tags)) {
				continue;
			}
			if (is_array($value)) {
				self::_text($value, []);
			} else {
				$value = strtr($value, ['"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;']);
			}
		}
	}*
}*/