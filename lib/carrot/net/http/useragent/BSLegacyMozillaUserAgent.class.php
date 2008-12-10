<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * レガシーMozillaユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSLegacyMozillaUserAgent extends BSUserAgent {

	/**
	 * @access public
	 * @param string $name ユーザーエージェント名
	 */
	public function __construct ($name = null) {
		parent::__construct($name);
		$this->attributes['is_msie'] = true;
		$this->bugs['css'] = true;
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '/^Mozilla\/[1-4]\..*\((Mac|Win|X11)/';
	}
}

/* vim:set tabstop=4 ai: */
?>