<?php
/**
 * Browseアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BrowseAction extends BSAction {
	private $logger;
	private $date;

	private function getLogger () {
		if (!$this->logger) {
			$this->logger = BSLogManager::getInstance()->getPrimaryLogger();
		}
		return $this->logger;
	}

	private function getDate () {
		if (!$this->date) {
			if ($this->request['date']) {
				$this->date = new BSDate($this->request['date']);
			} else {
				$this->date = $this->getLogger()->getLastDate();
				$this->request['date'] = $this->date->format('Y-m-d');
			}
		}
		return $this->date;
	}

	public function execute () {
		$this->request->setAttribute('dates', $this->getLogger()->getDates());
		$this->request->setAttribute('entries', $this->getLogger()->getEntries($this->getDate()));
		return BSView::SUCCESS;
	}

	public function handleError () {
		return $this->controller->forwardTo($this->controller->getNotFoundAction());
	}

	public function validate () {
		return ($this->getDate() && $this->getLogger());
	}
}

/* vim:set tabstop=4 ai: */
?>