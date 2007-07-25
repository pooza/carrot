<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage database
 */

/**
 * PDOのMySQL用ラッパー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSMySQL.class.php 261 2007-01-03 13:25:18Z pooza $
 */
class BSMySQL extends BSDatabase {
	private static $instance;

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSMySQL インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			try {
				$db = new BSMySQL(self::DSN, self::UID, self::PASSWORD);
				$db->dsn = self::DSN;
				preg_match('/^mysql:host=([^;]+);dbname=([^;]+)$/', $db->dsn, $matches);
				$db->host = new BSHost($matches[1]);
				$db->port = self::getDefaultPort();
				$db->name = $matches[2];
				$db->user = self::UID;
				$db->exec('SET NAMES ' . self::getEncoding());
				self::$instance = $db;
			} catch (Exception $e) {
				$e = new BSDatabaseException('DB接続エラーです。 (%s)', $e->getMessage());
				$e->sendNotify();
				throw $e;
			}
		}
		return self::$instance;
	}

	/**
	 * テーブル名のリストを配列で返す
	 *
	 * @access public
	 * @return string[] テーブル名のリスト
	 */
	public function getTableNames () {
		if (!$this->tables) {
			$nameColumn = 'Tables_in_' . $this->getDatabaseName();
			foreach ($this->query('SHOW TABLES') as $row) {
				$this->tables[] = $row[$nameColumn];
			}
		}
		return $this->tables;
	}

	/**
	 * 一時テーブルを生成、テーブルハンドラを返す
	 *
	 * @access public
	 * @param string[] $fields フィールド定義
	 * @return BSTemporaryTableHandler 生成された一時テーブル
	 */
	public function getTemporaryTable ($fields) {
		return new BSTemporaryTableHandler($fields);
	}

	/**
	 * クエリーログを書き込む
	 *
	 * @access protected
	 * @param string $query クエリーログ
	 */
	protected function putQueryLog ($query) {
		if (!preg_match('/^SET NAMES/i', $query)) {
			BSLog::put($query, self::LOG_TYPE);
		}
	}

	/**
	 * キャラクターセットを返す
	 *
	 * @access public
	 * @return string キャラクターセット
	 * @static
	 */
	public static function getEncoding () {
		$charsets = array(
			'sjis' => 'sjis',
			'euc-jp' => 'ujis',
			'utf-8' => 'utf8',
		);
		return $charsets[BSString::SCRIPT_ENCODING];
	}

	/**
	 * 規定のポート番号を返す
	 *
	 * @access public
	 * @return integer port
	 * @static
	 */
	public static function getDefaultPort () {
		return BSServiceList::getPort('mysql');
	}
}

/* vim:set tabstop=4 ai: */
?>