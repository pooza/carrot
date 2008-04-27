<?php
/**
 * SummarySuccessViewビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class SummarySuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');
	}
}

/* vim:set tabstop=4 ai: */
?>