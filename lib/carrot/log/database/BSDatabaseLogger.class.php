<?php
/**
 * @package org.carrot-framework
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
	private $dates;
	private $entries;
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
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSLogEntryHandler
	 */
	public function getTable () {
		return $this->table;
	}

	/**
	 * ログを出力
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
		$this->getTable()->createRecord($values);
	}

	/**
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return BSArray 日付の配列
	 */
	public function getDates () {
		if (!$this->dates) {
			$this->dates = new BSArray;
			foreach ($this->getTable()->getDates() as $date) {
				$date = new BSDate($date);
				$month = $date->format('Y-m');
				if (!$this->dates[$month]) {
					$this->dates[$month] = new BSArray;
				}
				$this->dates[$month][$date->format('Y-m-d')] = $date->format('Y-m-d(ww)');
			}
		}
		return $this->dates;
	}

	/**
	 * エントリーを抽出して返す
	 *
	 * @access public
	 * @param string BSDate 対象日付
	 * @return BSArray エントリーの配列
	 */
	public function getEntries (BSDate $date) {
		if (!$this->entries) {
			$this->entries = new BSArray;
			foreach ($this->getTable()->getEntries($date) as $entry) {
				$values = $entry->getAttributes();
				$values['exception'] = $entry->isException();
				$this->entries[] = $values;
			}
		}
		return $this->entries;
	}
}

/* vim:set tabstop=4 ai: */
?>