<?php
/**
 * Feedビュー
 *
 * @package org.carrot-framework
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class FeedView extends BSView {
	public function initialize () {
		parent::initialize();
		$this->setRenderer(new BSRSS20Document);
		return true;
	}

	public function execute () {
		$this->renderer->setTitle($this->controller->getHost()->getName());
		$this->renderer->setDescription(BS_APP_NAME_JA . 'の管理ログ');
		$this->renderer->setLink($this->getModule('AdminLog'));
		foreach ($this->request->getAttribute('entries') as $log) {
			$entry = $this->renderer->createEntry();
			$entry->setTitle($log['message']);
			$entry->setDate(BSDate::create($log['date']));
			$message = new BSArray(array(
				'date' => $log['date'],
				'remote_host' => $log['remote_host'],
				'priority' => $log['priority'],
			));
			$entry->setBody($message->join("\n", ': '));
		}
	}
}

/* vim:set tabstop=4: */
