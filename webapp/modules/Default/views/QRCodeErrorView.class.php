<?php
/**
 * QRCodeErrorビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class QRCodeErrorView extends BSView {
	public function execute () {
		$this->setStatus(404);
	}
}

