<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.http.useragent
 */

/**
 * 規定ユーザーエージェント
 *
 * マイナーブラウザ、ロボット等。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDefaultUserAgent extends BSUserAgent {

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '.*';
	}
}

