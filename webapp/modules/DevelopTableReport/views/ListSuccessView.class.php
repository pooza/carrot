<?php
/**
 * ListSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: ListSuccessView.class.php 311 2007-04-15 12:26:04Z pooza $
 */
class ListSuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('tables', $this->request->getAttribute('tables'));
	}
}

/* vim:set tabstop=4 ai: */
?>