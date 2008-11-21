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
	private $exception;

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
		$this->request->setAttribute('dates', array());
		$entry = array(
			'exception' => true,
			'date' => BSDate::getNow('Y-m-d H:i:s'),
			'remote_host' => $this->request->getHost()->getName(),
			'message' => 'ログを取得できません。',
		);
		if ($this->exception) {
			$entry['priority']= get_class($this->exception);
			$entry['message'] = $this->exception->getMessage();
		}
		$this->request->setAttribute('entries', array($entry));
		return BSView::SUCCESS;
	}

	public function validate () {
		try {
			return ($this->getDate() && $this->getLogger());
		} catch (BSLogException $e) {
			$this->exception = $e;
			return false;
		}
	}
}

/* vim:set tabstop=4 ai: */
?>