<?php
/**
 * @package org.carrot-framework
 * @subpackage view
 */

/**
 * 規定View
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDefaultView extends BSSmartyView {

	/**
	 * @access public
	 * @param BSAction $action 呼び出し元アクション
	 * @param string $suffix ビュー名サフィックス
	 */
	public function __construct (BSAction $action, $suffix = null) {
		parent::__construct($action);
		$this->nameSuffix = $suffix;
	}

	/**
	 * ビュー名を返す
	 *
	 * @access public
	 * @return string ビュー名
	 */
	public function getName () {
		if (!$this->name) {
			$this->name = $this->getAction() . $this->getNameSuffix();
		}
		return $this->name;
	}
}

/* vim:set tabstop=4: */
