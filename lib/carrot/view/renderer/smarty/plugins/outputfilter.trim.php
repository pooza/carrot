<?php
/**
 * @package org.carrot-framework
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * トリミング出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_outputfilter_trim ($source, &$smarty) {
	$source = trim($source);
	$source = mb_ereg_replace('[ \\t]*\\n\\t*', "\n", $source);
	return $source;
}

/* vim:set tabstop=4: */
