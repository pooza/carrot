<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopTableReport
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DefaultAction.class.php 175 2006-07-27 13:04:55Z pooza $
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->controller->forward('DevelopTableReport', 'List');
	}
}

/* vim:set tabstop=4 ai: */
?>