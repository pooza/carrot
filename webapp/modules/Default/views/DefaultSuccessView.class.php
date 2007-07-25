<?php
/**
 * DefaultSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DefaultSuccessView.class.php 862 2007-06-29 09:15:49Z pooza $
 */
class DefaultSuccessView extends BSSmartyView {
	public function execute () {
		$this->setTemplate($this->request->getParameter('document'));
	}
}

/* vim:set tabstop=4 ai: */
?>