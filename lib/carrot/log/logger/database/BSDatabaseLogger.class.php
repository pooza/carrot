<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger.database
 */

/**
 * データベース用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDatabaseLogger extends BSLogger {
	private $table;
	private $dates;
	private $entries;
	const TABLE_NAME = 'log_entry';

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		try {
			$this->table = BSTableHandler::create(self::TABLE_NAME);
			return true;
		} catch (BSDatabaseException $e) {
			return false;
		}
	}

	/**
	 * テーブルを返す
	 *
	 * @access public
	 * @return BSTableHandler テーブル
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
	public function put ($message, $priority) {
		$this->getTable()->createRecord([
			'date' => BSDate::getNow('Y-m-d H:i:s'),
			'remote_host' => BSRequest::getInstance()->getHost()->getName(),
			'priority' => $priority,
			'message' => $message,
		]);
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
				$date = BSDate::create($date);
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

/* vim:set tabstop=4: */
