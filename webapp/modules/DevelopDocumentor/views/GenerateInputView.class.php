<?php
/**
 * GenerateInputビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: GenerateInputView.class.php 354 2007-06-27 08:09:37Z pooza $
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

/* vim:set tabstop=4 ai: */
?>