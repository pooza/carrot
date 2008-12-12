<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty.plugins
 */

/**
 * ケータイ向け出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_outputfilter_mobile ($source, &$smarty) {
	$source = BSString::convertKana($source, 'kas');
	return $source;
}

/* vim:set tabstop=4: */

