<?php
/**
 * Browseアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BrowseAction extends BSAction {
	public function execute () {
		$logger = BSLogManager::getInstance()->getPrimaryLogger();

		if (!$month = $this->request['month']) {
			$this->request['month'] = $logger->getLastMonth();
		}

		$this->request->setAttribute('months', $logger->getMonths());
		$this->request->setAttribute('entries', $logger->getEntries($this->request['month']));
		return BSView::SUCCESS;
	}
}

/* vim:set tabstop=4 ai: */
?>