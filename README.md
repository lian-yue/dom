# dom


### test
	<?php

	error_reporting(E_ALL);
	set_time_limit(3);

	require_once  __DIR__ . '/Node.php';
	require_once  __DIR__ . '/Style/Base.php';
	require_once  __DIR__ . '/Style/Document.php';
	require_once  __DIR__ . '/Style/Media.php';
	require_once  __DIR__ . '/Style/MediaCondition.php';
	require_once  __DIR__ . '/Style/Rule.php';
	require_once  __DIR__ . '/Style/Selectors.php';
	require_once  __DIR__ . '/Style/Supports.php';



	// html  解析
	$contents = file_get_contents(__DIR__ . '/html.html');

	$node = new DOM\Node($contents);

	// 格式化的
	echo $node->format(true);

	// 不格式化的
	echo $node;

	// 选择器


	// 返回 node 对象的
	//  按照id 选择
	$node->getElementById('ID');

	// css选择器 选择一个
	$node->querySelector('css选择器');


	// 返回数组的

	// 按照 class 选择
	$node->getElementsByClassName('class');

	// 按照 name 选择
	$node->getElementsByName('class');

	//  按照标签名选择
	$node->getElementsByTagName('tagname');


	// css选择器 选择所有
	$node->querySelectorAll('div.qq#xx[attr="xx"]:has(img)');







	//  style 解析
	$contents = file_get_contents(__DIR__ . '/style.css');

	$rule = new DOM\Style\Rule($contents);

	// 格式化的
	echo $node->format(true);

	// 不格式化的
	echo $node;






	
	
	
	// 过滤数据演示

	// 创建style 过滤 保留 属性 content 开头的前缀  #content-  .content- 开头的保留
	$filterStyle = new DOM\Filter\Style('content-');
	
	// 创建标签过滤 传入了filterStyle就允许style 标签
	$filterTags = new DOM\Filter\Tags($filterStyle);
	
	
	// 创建属性过滤  传入了filterStyle就允许style 属性
	$filterAttributes = new DOM\Filter\Attributes($filterStyle);
	
	
	/// 解析 dom
	$node = new DOM\Node($contents);
	
	// 过滤标签
	$filterTags($node);
	
	// 过滤属性
	$filterAttributes($node);
	
	// 格式化并输出
	echo $node->format(true);













	?>
