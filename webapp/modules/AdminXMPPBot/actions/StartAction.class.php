<?php
/**
 * Startアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StartAction extends BSAction {
	public function execute () {
		$command = sprintf(
			'%s/carrotctl.php -s %s -a %s > /dev/null &',
			$this->controller->getPath('bin'),
			$this->controller->getServerHost()->getName(),
			'XMPPBot'
		);
		shell_exec($command);
		sleep(5);
		return $this->controller->redirect('/AdminXMPPBot/');
	}
}

/* vim:set tabstop=4 ai: */
?>