<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * トリミング出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_outputfilter_trim ($source, &$smarty) {
	return BSString::trim($source);
}

