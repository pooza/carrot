<?php
/**
 * AdminLogモジュール
 *
 * @package org.carrot-framework
 * @subpackage AdminLog
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class AdminLogModule extends BSModule {
	private $logger;

	/**
	 * 対象ロガーを返す
	 *
	 * @access public
	 * @return BSLogger ロガー
	 */
	public function getLogger () {
		if (!$this->logger) {
			$this->logger = BSLogManager::getInstance()->getPrimaryLogger();
		}
		return $this->logger;
	}

	/**
	 * 対象日付を返す
	 *
	 * @access public
	 * @return BSDate 日付
	 */
	public function getDate () {
		if ($this->request['date']) {
			return BSDate::create($this->request['date']);
		} else {
			return $this->getLogger()->getLastDate();
		}
	}

	/**
	 * 指定日付のログエントリーを返す
	 *
	 * @access public
	 * @return mixed ファイル又はディレクトリ
	 */
	public function getEntries (BSDate $date = null) {
		if (!$date) {
			$date = $this->getDate();
		}
		return $this->getLogger()->getEntries($date);
	}

	/**
	 * 日付配列を返す
	 *
	 * @access public
	 * @return mixed[][] 日付配列
	 */
	public function getDates () {
		return $this->getLogger()->getDates();
	}
}

/* vim:set tabstop=4: */
