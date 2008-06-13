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
		$dir = $this->controller->getDirectory('log');
		$this->request->setAttribute('logfiles', $dir->getDevidedEntryNames());

		if ($logfile = $this->request['logfile']) {
			$entry = $dir->getEntry($logfile);
		} else {
			$entry = $dir->getLatestEntry();
		}

		if ($entry) {
			$this->request->setAttribute('logfile', $entry->getBaseName());
			$this->request->setAttribute('logs', $entry->getContents());
		}

		return BSView::SUCCESS;
	}

	public function getRequestMethods () {
		return BSRequest::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>