<?php
/**
 * Stopアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StopAction extends BSAction {
	public function execute () {
		$xmpp = new BSXMPPBotClient($this->controller->getHost());
		$xmpp->putLine('/QUIT');
		sleep(1);
		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4: */
