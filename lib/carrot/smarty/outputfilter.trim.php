<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * トリミング出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: outputfilter.trim.php 339 2007-06-12 13:46:38Z pooza $
 */
function smarty_outputfilter_trim ($source, &$smarty) {
	return trim($source);
}
?>
