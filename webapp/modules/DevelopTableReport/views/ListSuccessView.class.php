<?php
/**
 * ListSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class ListSuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('tables', $this->request->getAttribute('tables'));
	}
}

/* vim:set tabstop=4 ai: */
?>