<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminXMPPBot
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DefaultAction.class.php 172 2006-07-27 11:12:57Z pooza $
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->controller->forward('AdminXMPPBot', 'Summary');
	}
}

/* vim:set tabstop=4 ai: */
?>