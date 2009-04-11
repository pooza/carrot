<?php
/**
 * FeedLogSuccessビュー
 *
 * @package org.carrot-framework
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LogSuccessView extends BSView {
	public function initialize () {
		parent::initialize();
		$this->setRenderer(BSClassLoader::getInstance()->getObject(BS_FEED_CLASS));
		return true;
	}

	public function execute () {
		$this->getEngine()->setTitle($this->controller->getHost()->getName());
		$this->getEngine()->setDescription(BSController::getName() . 'の管理ログ');
		$this->getEngine()->setLink($this->controller->getModule('AdminLog')->getURL());
		foreach ($this->request->getAttribute('entries') as $log) {
			$entry = $this->getEngine()->createEntry();
			$entry->setTitle($log['message']);
			$entry->setDate(new BSDate($log['date']));
			$message = array(
				'date' => $log['date'],
				'remote_host' => $log['remote_host'],
				'priority' => $log['priority'],
			);
			$entry->setBody(BSString::toString($message, ': ', "\n"));
		}
	}
}

/* vim:set tabstop=4: */
