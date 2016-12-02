<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * ケータイ向け出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_outputfilter_mobile ($source, &$smarty) {
	$source = BSString::convertKana($source, 'kas');
	return $source;
}

/* vim:set tabstop=4: */
