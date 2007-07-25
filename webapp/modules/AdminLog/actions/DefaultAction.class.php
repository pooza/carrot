<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: DefaultAction.class.php 176 2006-07-27 13:14:21Z pooza $
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->controller->forward('AdminLog', 'Browse');
	}
}

/* vim:set tabstop=4 ai: */
?>