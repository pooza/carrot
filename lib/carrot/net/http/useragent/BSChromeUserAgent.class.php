<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Chromeユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSChromeUserAgent extends BSWebKitUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_webkit'] = true;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/Chrome/';
	}
}

/* vim:set tabstop=4 ai: */
?>