<?php
/**
 * FeedLogアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: LogAction.class.php 300 2007-03-10 12:56:23Z pooza $
 */
class LogAction extends BSAction {
	public function execute () {
		$dir = $this->controller->getDirectory('log');
		$this->request->setAttribute('logs', $dir->getLatestEntry()->getContents());

		return View::SUCCESS;
	}

	public function getRequestMethods () {
		return Request::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>