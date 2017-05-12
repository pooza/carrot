<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.inline_frame
 */

/**
 * iframe要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSInlineFrameElement extends BSXHTMLElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('frameborder', 0);
		$this->setAttribute('scrolling', 'no');
		$this->createElement('p', 'インラインフレームに対応したブラウザをご利用ください。');
	}

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTag () {
		return 'iframe';
	}

	/**
	 * URLを設定
	 *
	 * @access public
	 * @param BSHTTPRedirector $url メディアのURL
	 */
	public function setURL (BSHTTPRedirector $url) {
		$this->setAttribute('src', $url->getURL()->getContents());
	}
}

