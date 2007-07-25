<?php
/**
 * PingSuccessビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: PingSuccessView.class.php 339 2007-06-12 13:46:38Z pooza $
 */
class PingSuccessView extends BSSmartyView {
	public function execute () {
		$this->setTemplate('DefaultMessage.Console');
		$this->getEngine()->setType(BSTypeList::getType('.txt'));
		$this->setAttribute('message', 'OK');
	}
}

/* vim:set tabstop=4 ai: */
?>