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
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		return $this->createCommand()->isExists();
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority) {
		$command = $this->createCommand();
		$line = new BSArray;
		$line[] = '[server ' . $this->getServerHostName() . ']';
		$line[] = '[' . $priority . ']';
		$line[] = '[client ' . $this->getClientHostName() . ']';
		$line[] = $message;
		$command->push('-p');
		if ($this->isException($priority)) {
			$command->push('local6.error');
		} else {
			$command->push('local6.info');
		}
		$command->push($line->join(' '));
		$command->execute();
	}

	private function createCommand () {
		$command = new BSCommandLine('bin/logger');
		$command->setDirectory(BSFileUtility::getDirectory('logger'));
		$command->push('-t');
		$command->push('carrot-' . $this->getServerHostName());
		return $command;
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
			if ($month = $this->getDates()[$date->format('Y-m')]) {
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
