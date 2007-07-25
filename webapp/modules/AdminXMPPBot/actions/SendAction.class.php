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

		$url = array(BSController::MODULE_ACCESSOR => 'AdminXMPPBot');
		return $this->controller->redirect($url);
	}

	public function getRequestMethods () {
		return Request::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>