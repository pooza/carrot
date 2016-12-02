<?php
/**
 * SummaryViewビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class SummaryView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');
	}
}

/* vim:set tabstop=4: */
