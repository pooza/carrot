<?php
/**
 * Stopアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StopAction extends BSAction {
	public function execute () {
		$xmpp = new BSXMPPBotClient($this->controller->getServerHost());
		$xmpp->putLine('/QUIT');
		sleep(1);
		return $this->controller->redirect($this->getModule());
	}
}

/* vim:set tabstop=4 ai: */
?>