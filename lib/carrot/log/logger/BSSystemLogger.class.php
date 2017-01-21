<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log.logger
 */

/**
 * syslog用ロガー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSystemLogger extends BSLogger {
	private $dates;
	private $entries;
	private $file;

	/**
	 * @access public
	 */
	public function __destruct () {
		closelog();
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		$constants = new BSConstantHandler('LOG');
		if (!$facility = $constants['SYSLOG_FACILITY']) {
			$facility = 'LOCAL6';
		}
		return openlog(
			'carrot-' . $this->getServerHostName(),
			LOG_PID,
			$constants[$facility]
		);
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority) {
		$line = new BSArray;
		$line[] = $this->getServerHostName();
		$line[] = $priority;
		$line[] = $this->getClientHostName();
		$line[] = $message;
		if ($this->isException($priority)) {
			syslog(LOG_ERR, $line->join(' '));
		} else {
			syslog(LOG_NOTICE, $line->join(' '));
		}
	}

	/**
	 * ログディレクトリを返す
	 *
	 * @access public
	 * @return BSLogDirectory ログディレクトリ
	 */
	public function getDirectory () {
		return BSFileUtility::getDirectory('log');
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
			foreach ($this->getDirectory() as $file) {
				if (!$date = BSDate::create($file->getBaseName())) {
					continue;
				}
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
			if ($month = $this->getDates()->getParameter($date->format('Y-m'))) {
				if ($month->hasParameter($name = $date->format('Y-m-d'))) {
					$file = $this->getDirectory()->getEntry($name);
					$this->entries->setParameters($file->getEntries());
				}
			}
		}
		return $this->entries;
	}
}

/* vim:set tabstop=4: */
