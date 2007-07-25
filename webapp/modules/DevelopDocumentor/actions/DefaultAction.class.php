<?php
/**
 * Defaultアクション
 *
 * @package jp.co.b-shock.carrot
 * @subpackage DevelopDocumentor
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class DefaultAction extends BSAction {
	public function execute () {
		return $this->controller->forward('DevelopDocumentor', 'Generate');
	}
}

/* vim:set tabstop=4 ai: */
?>