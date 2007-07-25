<?php
/**
 * Sendアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: SendAction.class.php 234 2006-12-02 10:01:51Z pooza $
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