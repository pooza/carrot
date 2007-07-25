<?php
/**
 * Stopアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: StopAction.class.php 234 2006-12-02 10:01:51Z pooza $
 */
class StopAction extends BSAction {
	public function execute () {
		$xmpp = new BSXMPPBotClient($this->controller->getServerHost());
		$xmpp->putLine('/QUIT');
		sleep(1);

		$url = array(BSController::MODULE_ACCESSOR => 'AdminXMPPBot');
		return $this->controller->redirect($url);
	}
}

/* vim:set tabstop=4 ai: */
?>