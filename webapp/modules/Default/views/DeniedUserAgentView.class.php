<?php
/**
 * DeniedUserAgentビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class DeniedUserAgentView extends BSSmartyView {
	public function execute () {
		$this->setStatus(400);
	}
}

/* vim:set tabstop=4: */
