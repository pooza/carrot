<?php
/**
 * @package org.carrot-framework
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
 * @version $Id$
 */
function smarty_block_jsonp ($params, $contents, &$smarty) {
	$params = new BSArray($params);
	$serializer = new BSJSONSerializer;
	return sprintf('%s(%s);', $params['method'], $serializer->encode($contents));
}

/* vim:set tabstop=4: */