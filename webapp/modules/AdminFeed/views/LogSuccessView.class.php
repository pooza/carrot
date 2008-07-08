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
	public function initialize () {
		parent::initialize();
		if (!$class = BSConstantHandler::getInstance()->getParameter('FEED_CLASS')) {
			$class = 'BSAtom10Document';
		}
		$this->setEngine(new $class);
		return true;
	}

	public function execute () {
		$this->getEngine()->setTitle($this->controller->getServerHost()->getName());
		$this->getEngine()->setDescription(BSController::getName() . 'の管理ログ');
		$this->getEngine()->setLink(BSModule::getInstance('AdminLog')->getURL());
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

/* vim:set tabstop=4 ai: */
?>