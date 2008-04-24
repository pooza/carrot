<?php
/**
 * FeedLogSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LogSuccessView extends BSView {
	const FEED_CLASS = BS_FEED_CLASS;

	public function initialize () {
		parent::initialize();
		$class = self::FEED_CLASS;
		$this->setEngine(new $class);
		return true;
	}

	public function execute () {
		$url = new BSURL();
		$url->setAttribute('path', '/AdminLog/');
		$this->getEngine()->setTitle($this->controller->getServerHost()->getName());
		$this->getEngine()->setDescription(BSController::getName() . 'の管理ログ');
		$this->getEngine()->setLink($url);

		foreach ($this->request->getAttribute('logs') as $log) {
			unset($log['exception']);
			$entry = $this->getEngine()->createEntry();
			$entry->setTitle($log['description']);
			$entry->setDate(new BSDate($log['date']));
			$entry->setBody(implode("\n", $log));
		}
	}
}

/* vim:set tabstop=4 ai: */
?>