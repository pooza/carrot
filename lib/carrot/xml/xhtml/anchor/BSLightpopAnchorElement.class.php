<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.anchor
 */

/**
 * jQuery.lightpopへのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLightpopAnchorElement extends BSImageAnchorElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->registerStyleClass('lightpop');
	}

	/**
	 * グループ名を設定
	 *
	 * @access public
	 * @param string $group グループ名
	 */
	public function setImageGroup ($group) {
		throw new BSMediaException(__CLASS__ . 'はsetImageGroupに非対応です。');
	}
}

/* vim:set tabstop=4: */
