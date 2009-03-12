<?php
/**
 * Pingアクション
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class PingAction extends BSAction {
	public function execute () {
		try {
			$renderer = new BSPlainTextRenderer;
			$renderer->setContents('OK');
			$db = $this->database;
			$this->request->setAttribute('renderer', $renderer);
			return BSView::SUCCESS;
		} catch (Exception $e) {
			$renderer->setContents('NG');
			return BSView::ERROR;
		}
	}
}

/* vim:set tabstop=4: */
