<?php
/**
 * @package org.carrot-framework
 * @subpackage smarty.plugins
 */

/**
 * トリミング出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_outputfilter_trim ($source, &$smarty) {
	return trim($source);
}

/* vim:set tabstop=4 ai: */
?>
