<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.anchor
 */

/**
 * Lityへのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLityAnchorElement extends BSImageAnchorElement {
	private $width;
	private $height;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('data-lity', 'data-lity');
	}
}

/* vim:set tabstop=4: */
