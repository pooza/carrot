<?php
/**
 * Listアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
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