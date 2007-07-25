<?php
/**
 * Detailアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DetailAction extends BSAction {
	public function execute () {
		$table = $this->database->getTableProfile($this->request->getParameter('table'));
		$this->request->setAttribute('tablename', $table->getName());
		$this->request->setAttribute('attributes', $table->getAttributes());
		$this->request->setAttribute('fields', $table->getFields());
		$this->request->setAttribute('keys', $table->getKeys());
		return View::SUCCESS;
	}

	public function getRequestMethods () {
		return Request::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>