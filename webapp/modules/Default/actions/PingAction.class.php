<?php
/**
 * Pingアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: PingAction.class.php 209 2006-09-02 18:04:33Z pooza $
 */
class PingAction extends BSAction {
	public function execute () {
		try {
			$db = $this->database;
			return View::SUCCESS;
		} catch (Exception $e) {
			return VIEW::ERROR;
		}
	}
}

/* vim:set tabstop=4 ai: */
?>