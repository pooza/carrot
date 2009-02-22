<?php
/**
 * @package org.carrot-framework
 * @subpackage view.smarty.plugins
 */

/**
 * メール文面用フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
function smarty_outputfilter_mail ($source, &$smarty) {
	$lines = BSString::explode("\n", $source);
	foreach ($lines as $key => $line) {
		if (BSString::isBlank($line)) { //空行を発見したらヘッダのパースをやめる
			$lines->removeParameter($key);
			break;
		} else if (preg_match('/^([a-z\-]+): *(.+)$/i', $line, $matches)) {
			$smarty->getHeaders()->setParameter($matches[1], $matches[2]);
			$lines->removeParameter($key);
		} else {
			break;
		}
	}
	$source = $lines->join("\n");
	$source = BSString::convertKana($source);
	$source = BSString::split($source, 78);
	$source = BSString::convertLineSeparator(BSMail::LINE_SEPARATOR, $source);
	return $source;
}

/* vim:set tabstop=4: */

