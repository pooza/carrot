<?php
/**
 * SummarySuccessViewビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: SummarySuccessView.class.php 354 2007-06-27 08:09:37Z pooza $
 */
class SummarySuccessView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');
		$this->setAttribute('pid', $this->request->getAttribute('pid'));
		$this->setAttribute('port', $this->request->getAttribute('port'));
		$this->setAttribute('from', $this->request->getAttribute('from'));
		$this->setAttribute('to', $this->request->getAttribute('to'));
	}
}

/* vim:set tabstop=4 ai: */
?>