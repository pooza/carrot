<?php
/**
 * GenerateInputビュー
 *
 * @package org.carrot-framework
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class GenerateInputView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');

		$directories = array(
			$this->controller->getPath('carrot') => 'lib/carrot',
			$this->controller->getPath('local_lib') => 'webapp/lib',
			$this->controller->getPath('modules') => 'webapp/modules',
		);
		$this->setAttribute('directory_options', $directories);

		$formats = array(
			'HTML:Smarty' => 'HTML:Smarty:default',
			'HTML:Smarty:PHP' => 'HTML:Smarty:PHP',
			'HTML:Smarty:HandS' => 'HTML:Smarty:HandS',
		);
		$this->setAttribute('formats', $formats);
	}
}

/* vim:set tabstop=4: */
