<?php
/**
 * NotFoundErrorビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: NotFoundErrorView.class.php 220 2006-10-03 16:27:00Z pooza $
 */
class NotFoundErrorView extends BSSmartyView {
	public function execute () {
		$this->setTemplate('DefaultMessage');
		$this->setAttribute('message', 'ファイルが見つかりません。');
	}
}

/* vim:set tabstop=4 ai: */
?>