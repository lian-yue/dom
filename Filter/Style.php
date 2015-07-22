<?php
/* ************************************************************************** */
/*
/*	Lian Yue
/*
/*	Url: www.lianyue.org
/*	Email: admin@lianyue.org
/*	Author: Moon
/*
/*	Created: UTC 2015-06-29 11:29:12
/*	Updated: UTC 2015-07-20 09:59:57
/*
/* ************************************************************************** */
namespace Loli\DOM\Filter;
class Style{

	// http://www.w3.org/Style/CSS/all-properties.en.html
	protected static $propertys = [

		//
		'align-content' => true,
		'align-items' => true,
		'align-self' => true,

		//
		'alignment-adjust' => true,
		'alignment-baseline' => true,

		// 全部
		'all' => true,

		// 动画
		'animation' => true,   						// 动画所有属性  <single-animation> [ ‘,’ <single-animation> ]*         <single-animation> = <single-animation-name> || <time> || <single-animation-timing-function> || <time> || <single-animation-iteration-count> || <single-animation-direction> || <single-animation-fill-mode> || <single-animation-play-state>
		'animation-delay' => true,					// 动画执行延迟  <time> [, <time>]*
		'animation-direction' => true,				// 动画运动方式  <single-animation-direction> [ ‘,’ <single-animation-direction> ]*            normal | reverse | alternate | alternate-reverse
		'animation-duration' => true,				// 动画运动时间  <time> [, <time>]*
		'animation-fill-mode' => true,				// 动画        <single-animation-fill-mode> [ ‘,’ <single-animation-fill-mode> ]*         <single-animation-fill-mode> = none | forwards | backwards | both
		'animation-iteration-count' => true,		// 动画
		'animation-name' => true,					// 使用动画名称
		'animation-play-state' => true,				// 动画是否暂停
		'animation-timing-function' => true,

		'azimuth' => true,

		'backface-visibility' => true,


		// 背景
		'background' => true,						// 背景所有属性
		'background-attachment' => true,			// 背景显示方式 固定 跟随滚动还是。。。
		'background-blend-mode' =>	true,			// 背景混合选项
		'background-clip' => true,					// 背景
		'background-color' => true,					// 背景颜色
		'background-image' => true,					// 背景图片
		'background-origin' => true,				// 背景定位方式
		'background-position' => true,				// 背景定位
		'background-repeat' => true,				// 背景重复
		'background-size' => true,					// 背景大小

		//
		'baseline-shift' => true,					//


		'bookmark-label' => true,					//
		'bookmark-level' => true,					//
		'bookmark-state' => true,					//


		// 边框
		'border' => true,							// 所有属性支持
		'border-bottom' => true,					// 底部
		'border-bottom-color' => true,				// 底部颜色
		'border-bottom-left-radius' => true,		// 底部左边圆角
		'border-bottom-right-radius' => true,		// 底部右边圆角
		'border-bottom-style' => true,				// 底部边框样式
		'border-bottom-width' => true,				// 边框宽度
		'border-collapse' => true,					// 
		'border-color' => true,						//
		'border-image' => true,
		'border-image-outset' => true,
		'border-image-repeat' => true,
		'border-image-slice' => true,
		'border-image-source' => true,
		'border-image-width' => true,
		'border-left' => true,
		'border-left-color' => true,
		'border-left-style' => true,
		'border-left-width' => true,
		'border-radius' => true,
		'border-right' => true,
		'border-right-color' => true,
		'border-right-style' => true,
		'border-right-width' => true,
		'border-spacing' => true,
		'border-style' => true,
		'border-top' => true,
		'border-top-color' => true,
		'border-top-left-radius' => true,
		'border-top-right-radius' => true,
		'border-top-style' => true,
		'border-top-width' => true,
		'border-width' => true,

		// 距离底部位置
		'bottom' => true,


		// 盒子
		'box-decoration-break' => true,
		'box-shadow' => true,		// 盒子投影
		'box-sizing' => true,		// 并排边框的向外还是向内
		'box-snap' => true,
		'box-suppress' => true,



		'break-after' => true,
		'break-before' => true,
		'break-inside' => true,
		'caption-side' => true,
		'caret-color' => true,
		'chains' => true,
		'clear' => true,
		'clip' => true,
		'clip-path' => true,
		'clip-rule' => true,
		'color' => true,
		'color-interpolation-filters' => true,
		'column-count' => true,
		'column-fill' => true,
		'column-gap' => true,
		'column-rule' => true,
		'column-rule-color' => true,
		'column-rule-style' => true,
		'column-rule-width' => true,
		'column-span' => true,
		'column-width' => true,
		'columns' => true,
		'content' => true,
		'counter-increment' => true,
		'counter-reset' => true,
		'counter-set' => true,
		'crop' => true,
		'cue' => true,
		'cue-after' => true,
		'cue-before' => true,
		'cursor' => true,
		'direction' => true,
		'display' => true,
		'display-inside' => true,
		'display-list' => true,
		'display-outside' => true,
		'dominant-baseline' => true,
		'drop-initial-after-adjust' => true,
		'drop-initial-after-align' => true,
		'drop-initial-before-adjust' => true,
		'drop-initial-before-align' => true,
		'drop-initial-size' => true,
		'drop-initial-value' => true,
		'elevation' => true,
		'empty-cells' => true,
		'filter' => true,
		'flex' => true,
		'flex-basis' => true,
		'flex-direction' => true,
		'flex-flow' => true,
		'flex-grow' => true,
		'flex-shrink' => true,
		'flex-wrap' => true,
		'float' => true,
		'flood-color' => true,
		'flood-opacity' => true,
		'flow' => true,
		'flow-from' => true,
		'flow-into' => true,
		'font' => true,
		'font-family' => true,
		'font-feature-settings' => true,
		'font-kerning' => true,
		'font-language-override' => true,
		'font-size' => true,
		'font-size-adjust' => true,
		'font-stretch' => true,
		'font-style' => true,
		'font-synthesis' => true,
		'font-variant' => true,
		'font-variant-alternates' => true,
		'font-variant-caps' => true,
		'font-variant-east-asian' => true,
		'font-variant-ligatures' => true,
		'font-variant-numeric' => true,
		'font-variant-position' => true,
		'font-weight' => true,
		'footnote-display' => true,
		'footnote-policy' => true,
		'grid' => true,
		'grid-area' => true,
		'grid-auto-columns' => true,
		'grid-auto-flow' => true,
		'grid-auto-rows' => true,
		'grid-column' => true,
		'grid-column-end' => true,
		'grid-column-start' => true,
		'grid-row' => true,
		'grid-row-end' => true,
		'grid-row-start' => true,
		'grid-template' => true,
		'grid-template-areas' => true,
		'grid-template-columns' => true,
		'grid-template-rows' => true,
		'hanging-punctuation' => true,
		'height' => true,
		'hyphens' => true,
		'image-orientation' => true,
		'image-resolution' => true,
		'initial-letter' => true,
		'initial-letter-align' => true,
		'inline-box-align' => true,
		'isolation' => true,
		'justify-content' => true,
		'justify-items' => true,
		'justify-self' => true,
		'left' => true,
		'letter-spacing' => true,
		'lighting-color' => true,
		'line-break' => true,
		'line-grid' => true,
		'line-height' => true,
		'line-snap' => true,
		'line-stacking' => true,
		'line-stacking-ruby' => true,
		'line-stacking-shift' => true,
		'line-stacking-strategy' => true,
		'list-style' => true,
		'list-style-image' => true,
		'list-style-position' => true,
		'list-style-type' => true,
		'margin' => true,
		'margin-bottom' => true,
		'margin-left' => true,
		'margin-right' => true,
		'margin-top' => true,
		'marker-side' => true,
		'marquee-direction' => true,
		'marquee-loop' => true,
		'marquee-speed' => true,
		'marquee-style' => true,
		'mask' => true,
		'mask-border' => true,
		'mask-border-mode' => true,
		'mask-border-outset' => true,
		'mask-border-repeat' => true,
		'mask-border-slice' => true,
		'mask-border-source' => true,
		'mask-border-width' => true,
		'mask-clip' => true,
		'mask-composite' => true,
		'mask-image' => true,
		'mask-mode' => true,
		'mask-origin' => true,
		'mask-position' => true,
		'mask-repeat' => true,
		'mask-size' => true,
		'mask-type' => true,
		'max-height' => true,
		'max-lines' => true,
		'max-width' => true,
		'max-zoom' => true,
		'min-height' => true,
		'min-width' => true,
		'min-zoom' => true,
		'mix-blend-mode' => true,
		'motion' => true,
		'motion-offset' => true,
		'motion-path' => true,
		'motion-rotation' => true,
		'move-to' => true,
		'nav-down' => true,
		'nav-left' => true,
		'nav-right' => true,
		'nav-up' => true,
		'object-fit' => true,
		'object-position' => true,
		'offset-after' => true,
		'offset-before' => true,
		'offset-end' => true,
		'offset-start' => true,
		'opacity' => true,
		'order' => true,
		'orientation' => true,
		'orphans' => true,
		'outline' => true,
		'outline-color' => true,
		'outline-offset' => true,
		'outline-style' => true,
		'outline-width' => true,
		'overflow' => true,
		'overflow-style' => true,
		'overflow-wrap' => true,
		'overflow-x' => true,
		'overflow-y' => true,
		'padding' => true,
		'padding-bottom' => true,
		'padding-left' => true,
		'padding-right' => true,
		'padding-top' => true,
		'page' => true,
		'page-break-after' => true,
		'page-break-before' => true,
		'page-break-inside' => true,
		'page-policy' => true,
		'pause' => true,
		'pause-after' => true,
		'pause-before' => true,
		'perspective' => true,
		'perspective-origin' => true,
		'pitch' => true,
		'pitch-range' => true,
		'play-during' => true,
		'position' => true,
		'presentation-level' => true,
		'quotes' => true,
		'region-fragment' => true,
		'resize' => true,
		'resolution' => true,
		'rest' => true,
		'rest-after' => true,
		'rest-before' => true,
		'richness' => true,
		'right' => true,
		'rotation' => true,
		'rotation-point' => true,
		'ruby-align' => true,
		'ruby-merge' => true,
		'ruby-position' => true,
		'running' => true,
		'scroll-snap-coordinate' => true,
		'scroll-snap-destination' => true,
		'scroll-snap-points-x' => true,
		'scroll-snap-points-y' => true,
		'scroll-snap-type' => true,
		'shape-image-threshold' => true,
		'shape-margin' => true,
		'shape-outside' => true,
		'size' => true,
		'speak' => true,
		'speak-as' => true,
		'speak-header' => true,
		'speak-numeral' => true,
		'speak-punctuation' => true,
		'speech-rate' => true,
		'stress' => true,
		'string-set' => true,
		'tab-size' => true,
		'table-layout' => true,
		'text-align' => true,
		'text-align-last' => true,
		'text-combine-upright' => true,
		'text-decoration' => true,
		'text-decoration-color' => true,
		'text-decoration-line' => true,
		'text-decoration-skip' => true,
		'text-decoration-style' => true,
		'text-emphasis' => true,
		'text-emphasis-color' => true,
		'text-emphasis-position' => true,
		'text-emphasis-style' => true,
		'text-height' => true,
		'text-indent' => true,
		'text-justify' => true,
		'text-orientation' => true,
		'text-overflow' => true,
		'text-shadow' => true,
		'text-transform' => true,
		'text-underline-position' => true,
		'top' => true,
		'transform' => true,
		'transform-origin' => true,
		'transform-style' => true,
		'transition' => true,
		'transition-delay' => true,
		'transition-duration' => true,
		'transition-property' => true,
		'transition-timing-function' => true,
		'unicode-bidi' => true,
		'user-zoom' => true,
		'vertical-align' => true,
		'visibility' => true,
		'voice-balance' => true,
		'voice-duration' => true,
		'voice-family' => true,
		'voice-pitch' => true,
		'voice-range' => true,
		'voice-rate' => true,
		'voice-stress' => true,
		'voice-volume' => true,
		'volume' => true,
		'white-space' => true,
		'widows' => true,
		'width' => true,
		'will-change' => true,
		'word-break' => true,
		'word-spacing' => true,
		'word-wrap' => true,
		'wrap-flow' => true,
		'wrap-through' => true,
		'writing-mode' => true,
		'z-index' => true,
		'zoom' => true,
	];


	// 允许匹配的 前缀
	public $prefix = 'content-';

	public function __construct($prefix = NULL) {
		if ($prefix !== NULL) {
			$this->prefix = $prefix;
		}
	}

	public function __invoke() {
		return call_user_func_array([$this, 'filter'], func_get_args());
	}

	public function filrer($name, $value) {
		// 变量的
		if (substr($name, 0, 2) === '--') {
			return substr($name, 0, 10) === '--content-';
		}
		if (!isset(self::$propertys[$name])) {
			return false;
		}
		// 禁止 position 的浏览器绝对定位
		if ($name === 'position') {
			return in_array(strtolower($value), ['static', 'absolute', 'relative', 'initial', 'inherit'], true);
		}
		return true;
	}
}