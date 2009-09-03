<?php
/**
 * NotFoundErrorビュー
 *
 * @package org.carrot-framework
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class NotFoundErrorView extends BSSmartyView {
	public function execute () {
		try {
			$this->setTemplate('NotFound');
		} catch (BSSmartyException $e) {
			$this->setTemplate('DefaultMessage');
			$this->setAttribute('message', 'ファイルが見つかりません。');
		}
		$this->setStatus(404);
	}
}

/* vim:set tabstop=4: */
