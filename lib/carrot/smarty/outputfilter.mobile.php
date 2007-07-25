<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage smarty
 */

/**
 * ケータイ向け出力フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: outputfilter.mobile.php 238 2006-12-03 04:54:37Z pooza $
 */
function smarty_outputfilter_mobile ($source, &$smarty) {
	$source = BSString::convertKana($source, 'kas');
	$source = BSString::convertEncoding($source, 'sjis', BSString::TEMPLATE_ENCODING);
	return $source;
}
?>
