<?php
/**
 * FeedLogアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminFeed
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class LogAction extends BSAction {
	public function execute () {
		$dir = $this->controller->getDirectory('log');
		$this->request->setAttribute('logs', $dir->getLatestEntry()->getContents());

		return BSView::SUCCESS;
	}

	public function getRequestMethods () {
		return BSRequest::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>