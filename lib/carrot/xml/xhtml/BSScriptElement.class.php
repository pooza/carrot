<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml
 */

/**
 * script要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSScriptElement extends BSXHTMLElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		if (!BS_VIEW_HTML5) {
			$this->setAttribute('type', 'text/javascript');
			if (!$this->getUserAgent()->isMobile()) {
				$this->setAttribute('charset', 'utf-8');
			}
		}
	}

	/**
	 * 本文を設定
	 *
	 * @access public
	 * @param string $body 本文
	 */
	public function setBody ($body = null) {
		if ($body instanceof BSStringFormat) {
			$body = $body->getContents();
		}

		BSUtility::includeFile('jsmin');
		$body = BSString::convertEncoding($body, 'utf-8');
		$body = ltrim(JSMin::minify($body));
		$this->body = $body;
		$this->contents = null;
	}
}

/* vim:set tabstop=4: */
