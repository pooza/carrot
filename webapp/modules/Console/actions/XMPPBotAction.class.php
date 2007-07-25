<?php
/**
 * XMPPBotアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Console
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: XMPPBotAction.class.php 172 2006-07-27 11:12:57Z pooza $
 */
class XMPPBotAction extends BSAction {
	public function execute () {
		BSXMPPBotDaemon::start();
		return View::NONE;
	}
}

/* vim:set tabstop=4 ai: */
?>