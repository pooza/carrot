<?php
/**
 * @package org.carrot-framework
 * @subpackage service.blog
 */

/**
 * ブログ更新Pingリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSBlogUpdatePingRequest extends BSXMLDocument {
	private $params;

	/**
	 * @access public
	 * @param string $name 要素の名前
	 */
	public function __construct ($name = 'methodCall') {
		parent::__construct($name);
		$this->createElement('methodName', 'weblogUpdates.ping');
		$this->params = $this->createElement('params');
	}

	/**
	 * param要素を加える
	 *
	 * @access public
	 * @param string $value 値
	 */
	public function registerParameter ($value) {
		if (BSString::isBlank($value)) {
			return;
		}
		$element = $this->params->createElement('param')->createElement('value');
		$element->setBody($value);
	}
}

/* vim:set tabstop=4: */
