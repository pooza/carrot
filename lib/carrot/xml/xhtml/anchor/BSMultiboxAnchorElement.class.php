<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml.anchor
 */

/**
 * multiBoxへのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMultiboxAnchorElement extends BSImageAnchorElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->registerStyleClass('mb');
	}

	/**
	 * グループ名を設定
	 *
	 * @access public
	 * @param string $group グループ名
	 */
	public function setImageGroup ($group) {
		$this->registerStyleClass($group);
	}
}

/* vim:set tabstop=4: */
