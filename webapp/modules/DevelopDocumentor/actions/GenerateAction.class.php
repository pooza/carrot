<?php
/**
 * Generateアクション
 *
 * @package org.carrot-framework
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class GenerateAction extends BSAction {
	public function execute () {
		$command = new BSCommandLine('bin/phpdoc');
		$command->setDirectory(new BSDirectory('/usr/local'));
		$command->addValue('-d');
		foreach ($this->request['directories'] as $dir) {
			$command->addValue($dir);
			$command->addValue(',');
		}
		$command->addValue('-t');
		$command->addValue($this->controller->getPath('doc'));
		$command->addValue('-o');
		$command->addValue($this->request['format']);
		$command->setSleepSeconds(2);
		$command->setBackground(true);
		$command->execute();

		if ($command->hasError()) {
			throw new BSConsoleException($command->getResult());
		}

		return $this->controller->redirect($this->getModule());
	}

	public function getDefaultView () {
		if (!$this->request['directories']) {
			$this->request['directories'] = array(
				$this->controller->getPath('carrot'),
				$this->controller->getPath('local_lib'),
			);
		}
		return BSView::INPUT;
	}

	public function handleError () {
		return BSView::INPUT;
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>