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
		$command = sprintf(
			'/usr/bin/env phpdoc -d %s -t %s -o %s > /dev/null &',
			implode(',', $this->request->getParameter('directories')),
			$this->controller->getPath('doc'),
			$this->request->getParameter('format')
		);
		shell_exec($command);
		sleep(2);
		return $this->controller->redirect('/DevelopDocumentor/');
	}

	public function getDefaultView () {
		if (!$this->request->hasParameter('directories')) {
			$directories = array(
				$this->controller->getPath('carrot'),
				$this->controller->getPath('local_lib'),
			);
			$this->request->setParameter('directories', $directories);
		}
		return BSView::INPUT;
	}

	public function handleError () {
		return BSView::INPUT;
	}

	public function validate () {
		if (!$this->request->getParameter('directories')) {
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