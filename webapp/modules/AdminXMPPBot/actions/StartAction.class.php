<?php
/**
 * Startアクション
 *
 * @package org.carrot-framework
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class StartAction extends BSAction {
	public function execute () {
		$command = new BSCommandLine('/carrotctl.php');
		$command->setDirectory($this->controller->getDirectory('bin'));
		$command->addValue('-' . BSController::ACTION_ACCESSOR);
		$command->addValue('XMPPBot');
		$command->setBackground(true);
		$command->setSleepSeconds(5);
		$command->execute();

		if ($command->hasError()) {
			throw new BSConsoleException($command->getResult());
		}

		return $this->getModule()->redirect();
	}
}

/* vim:set tabstop=4 ai: */
?>