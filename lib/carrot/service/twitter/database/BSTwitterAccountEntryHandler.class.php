<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter.database
 */

/**
 * Twitterアカウント エントリーテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTwitterAccountEntryHandler extends BSTableHandler {

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * サロゲートキーを持つテーブルか？
	 *
	 * @access protected
	 * @return boolean サロゲートキーを持つならTrue
	 */
	protected function hasSurrogateKey () {
		return false;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return 'account';
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		return BSDatabase::getInstance('twitter');
	}

	/**
	 * スキーマを返す
	 *
	 * @access public
	 * @return BSArray フィールド情報の配列
	 */
	public function getSchema () {
		return new BSArray(array(
			'id' => 'integer NOT NULL PRIMARY KEY',
			'screen_name' => 'varchar(64)',
			'oauth_token' => 'varchar(64)',
			'oauth_token_secret' => 'varchar(64)',
		));
	}
}

/* vim:set tabstop=4: */
