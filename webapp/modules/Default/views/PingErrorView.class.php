<?php
/**
 * PingErrorビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class PingErrorView extends BSSmartyView {
	public function execute () {
		$this->setTemplate('DefaultMessage.Console');
		$this->getEngine()->setType(BSTypeList::getType('.txt'));
		$this->setAttribute('message', 'NG');
	}
}

/* vim:set tabstop=4 ai: */
?>