<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.database
 */

/**
 * データベース用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDatabaseLogger extends BSLogger {
	private $table;
	const TABLE_NAME = 'log';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		$this->table = new BSLogEntryHandler;
	}

	/**
	 * ログを出力する
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = self::DEFAULT_PRIORITY) {
		$values = array(
			'date' => BSDate::getNow('Y-m-d H:i:s'),
			'remote_host' => BSController::getInstance()->getClientHost()->getName(),
			'priority' => $priority,
			'message' => $message,
		);
		$this->table->createRecord($values);
	}

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return BSArray 月の配列
	 */
	public function getMonths () {
		return $this->table->getMonths();
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string $month yyyy-mm形式の月
	 * @return BSArray エントリーの配列
	 */
	public function getEntries ($month) {
		return new BSArray($this->table->getEntries($month)->getContents());
	}
}

/* vim:set tabstop=4 ai: */
?>