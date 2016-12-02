<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.anchor
 */

/**
 * Lightboxへのリンク
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLightboxAnchorElement extends BSImageAnchorElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('rel', 'lightbox');
	}

	/**
	 * グループ名を設定
	 *
	 * @access public
	 * @param string $group グループ名
	 */
	public function setImageGroup ($group) {
		if (BSString::isBlank($group)) {
			$this->setAttribute('rel', 'lightbox');
		} else {
			$this->setAttribute('rel', 'lightbox[' . $group . ']');
		}
	}
}

/* vim:set tabstop=4: */
