<?php
/**
 * SummaryViewビュー
 *
 * @package org.carrot-framework
 * @subpackage AdminMemcache
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class SummaryView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');
	}
}

/* vim:set tabstop=4: */
