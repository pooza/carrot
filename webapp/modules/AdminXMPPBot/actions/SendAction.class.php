<?php
/**
 * Sendアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SendAction extends BSAction {
	public function execute () {
		if ($command = $this->request['command']) {
			$xmpp = new BSXMPPBotClient($this->controller->getHost());
			$xmpp->putLine($command);
			sleep(1);
		}
		return $this->getModule()->redirect();
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4: */
