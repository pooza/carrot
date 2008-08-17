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
			$db = $this->database;
			return BSView::SUCCESS;
		} catch (Exception $e) {
			return BSView::ERROR;
		}
	}
}

/* vim:set tabstop=4 ai: */
?>