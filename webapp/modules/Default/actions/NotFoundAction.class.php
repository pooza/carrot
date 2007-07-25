<?php
/**
 * NotFoundアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class NotFoundAction extends BSAction {
	public function execute () {
		return View::ERROR;
	}

	public function getDefaultView () {
		return View::ERROR;
	}

	public function getRequestMethods () {
		return Request::GET;
	}
}

/* vim:set tabstop=4 ai: */
?>