<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty
 */

/**
 * ケータイ向け出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
function smarty_outputfilter_mobile ($source, &$smarty) {
	$source = BSString::convertKana($source, 'kas');
	$source = BSString::convertEncoding($source, 'sjis', 'utf-8');
	return $source;
}

/* vim:set tabstop=4 ai: */
?>
