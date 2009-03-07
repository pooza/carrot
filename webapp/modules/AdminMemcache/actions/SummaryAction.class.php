<?php
/**
 * Summaryアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SummaryAction extends BSAction {
	public function execute () {
		$manager = BSMemcacheManager::getInstance();
exit;
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */
