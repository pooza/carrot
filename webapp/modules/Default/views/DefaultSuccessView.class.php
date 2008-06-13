<?php
/**
 * DefaultSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultSuccessView extends BSSmartyView {
	public function execute () {
		$this->setTemplate($this->request['document']);
	}
}

/* vim:set tabstop=4 ai: */
?>