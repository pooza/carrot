<?php
/**
 * Listアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: ListAction.class.php 159 2006-07-16 08:26:26Z pooza $
 */
class ListAction extends BSAction {
	public function execute () {
		$this->request->setAttribute('tables', $this->database->getTableNames());
		return View::SUCCESS;
	}

	public function getRequestMethods () {
		return Request::GET | Request::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>