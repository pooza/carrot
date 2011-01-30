<?php
/**
 * Summaryアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>

 */
class SummaryAction extends BSAction {
	public function execute () {
		try {
			if ($server = BSMemcacheManager::getInstance()->getServer()) {
				$this->request->setAttribute('server', $server->getAttributes());
			}
		} catch (Exception $e) {
		}
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */
