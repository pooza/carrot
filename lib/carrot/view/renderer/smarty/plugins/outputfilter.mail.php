<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage view.renderer.smarty.plugins
 */

/**
 * メール文面用フィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
function smarty_outputfilter_mail ($source, &$smarty) {
	$mime = new BSMail;
	$mime->setContents($source);
	foreach ($mime->getHeaders() as $header) {
		$smarty->getHeaders()->setParameter($header->getName(), $header->getEntity());
	}
	$source = $mime->getBody();
	return $source;
}

/* vim:set tabstop=4: */
