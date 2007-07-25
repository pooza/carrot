<?php
/**
 * DetailSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DetailSuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('tablename', $this->request->getAttribute('tablename'));
		$this->setAttribute('attributes', $this->request->getAttribute('attributes'));
		$this->setAttribute('fields', $this->request->getAttribute('fields'));
		$this->setAttribute('keys', $this->request->getAttribute('keys'));
	}
}

/* vim:set tabstop=4 ai: */
?>