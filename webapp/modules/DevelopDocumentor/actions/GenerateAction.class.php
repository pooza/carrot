<?php
/**
 * Generateアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class GenerateAction extends BSAction {
	public function execute () {
		$command = new BSCommandLine('bin/phpdoc');
		$command->setDirectory(new BSDirectory('/usr/local'));
		$command->addValue('-d', null);
		foreach ($this->request['directories'] as $dir) {
			$command->addValue($dir);
			$command->addValue(',', null);
		}
		$command->addValue('-t', null);
		$command->addValue($this->controller->getPath('doc'));
		$command->addValue('-o', null);
		$command->addValue($this->request['format']);
		$command->setSleepSeconds(2);
		$command->setBackground(true);
		$command->execute();

		if ($command->getReturnCode()) {
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

	public function validate () {
		if (!$this->request['directories']) {
			$this->request->setError('directories', 'ディレクトリが選ばれていません。');
		}
		return (count($this->request->getErrors()) == 0);
	}

	public function getRequestMethods () {
		return BSRequest::POST;
	}
}

/* vim:set tabstop=4 ai: */
?>