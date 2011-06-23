<?php
/**
 * Browseアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BrowseAction extends BSAction {
	private $exception;

	/**
	 * タイトルを返す
	 *
	 * @access public
	 * @return string タイトル
	 */
	public function getTitle () {
		return '管理ログ';
	}

	public function execute () {
		$this->request->setAttribute('dates', $this->getModule()->getDates());
		$this->request->setAttribute('entries', $this->getModule()->getEntries());
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
			return !!$this->getModule()->getLogger();
		} catch (BSLogException $e) {
			$this->exception = $e;
			return false;
		}
	}
}

/* vim:set tabstop=4: */
