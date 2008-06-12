<?php
/**
 * Sendアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SendAction extends BSAction {
	public function execute () {
		if ($command = $this->request->getParameter('command')) {
			$xmpp = new BSXMPPBotClient($this->controller->getServerHost());
			$xmpp->putLine($command);
			sleep(1);
		}
		return $this->controller->redirect($this->getModule());
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>