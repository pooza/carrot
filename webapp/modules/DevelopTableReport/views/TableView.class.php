<?php
/**
 * Tableビュー
 *
 * @package org.carrot-framework
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class TableView extends BSSmartyView {
	public function execute () {
		$this->setAttribute('styleset', 'carrot.Detail');
	}
}

/* vim:set tabstop=4: */
