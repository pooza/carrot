<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * ブロック内をJSONP化 
 *
 * クライアント側:
 * <script type="text/javascript">
 * function callback (contents) {
 *   $('container').innerHTML = contents;
 * }
 * actions.onload.push(function () {
 *   var script = document.createElement('script');
 *   script.src = '/hoge.js';
 *   document.body.appendChild(script);
 * });
 * </script>
 *
 * サーバ側:
 * {jsonp method='callback'}
 * クライアントに渡すデータ。
 * {/jsonp}
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_block_jsonp ($params, $contents, &$smarty) {
	$params = BSArray::create($params);
	$serializer = new BSJSONSerializer;
	$body = new BSStringFormat('%s(%s);');
	$body[] = $params['method'];
	$body[] = $serializer->encode($contents);
	return $body->getContents();
}

/* vim:set tabstop=4: */
