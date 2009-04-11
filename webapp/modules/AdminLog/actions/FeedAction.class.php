<?php
/**
 * Feedアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class FeedAction extends BSAction {
	public function execute () {
		$logger = BSLogManager::getInstance()->getPrimaryLogger();
		$this->request->setAttribute('entries', $logger->getEntries($logger->getLastDate()));
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4: */
