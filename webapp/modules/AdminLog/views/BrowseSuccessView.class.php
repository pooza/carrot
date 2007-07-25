<?php
/**
 * BrowseSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BrowseSuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('logfiles', $this->request->getAttribute('logfiles'));
		$this->setAttribute('logfile', $this->request->getAttribute('logfile'));
		$this->setAttribute('logs', $this->request->getAttribute('logs'));
	}
}

/* vim:set tabstop=4 ai: */
?>