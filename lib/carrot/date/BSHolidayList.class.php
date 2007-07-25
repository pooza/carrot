<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage date
 */

/**
 * 祝日リスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSHolidayList extends BSList {
	protected $url;
	protected $rules = array();

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		$expire = BSDate::getNow();
		$expire->setAttribute('month', '-1');

		if (!$this->attributes) {
			$this->attributes = BSController::getInstance()->getAttribute(
				$this->getName(), $expire
			);
			if (!$this->attributes) {
				$this->setRules();
				$this->setHolidays();
				BSController::getInstance()->setAttribute(
					$this->getName(), $this->attributes
				);
			}
		}
		return $this->attributes;
	}

	/**
	 * 開始日付を返す
	 *
	 * @access public
	 * @return BSDate 開始日付
	 */
	public function getStartDate () {
		$date = BSDate::getNow();
		$date->setAttribute('month', '-1');
		$date->setAttribute('hour', 0);
		$date->setAttribute('minute', 0);
		$date->setAttribute('second', 0);
		return $date;
	}

	/**
	 * 終了日付を返す
	 *
	 * @access public
	 * @return BSDate 終了日付
	 */
	public function getFinishDate () {
		$date = BSDate::getNow();
		$date->setAttribute('year', '+1');
		$date->setAttribute('hour', 0);
		$date->setAttribute('minute', 0);
		$date->setAttribute('second', 0);
		return $date;
	}

	/**
	 * ICSカレンダーの内容を返す
	 *
	 * @access protected
	 * @return string カレンダーの内容
	 */
	protected function getBody () {
		try {
			$http = new BSCurlHTTP($this->getURL()->getAttribute('host'));
			$http->setAttribute('http_version', CURL_HTTP_VERSION_1_1);
			$body = $http->getContents($this->getURL()->getAttribute('path'));
		} catch (Exception $e) {
			throw new BSDateException('カレンダーを取得出来ません。');
		}

		$body = BSString::convertEncoding($body, null, 'utf-8');
		return $body;
	}

	/**
	 * 祝日の配列を返す
	 *
	 * @access protected
	 * @param BSDate $start 開始日
	 * @param BSDate $finish 終了日
	 * @return string[] 祝日の配列
	 */
	public function getHolidays (BSDate $start = null, BSDate $finish = null) {
		if (!$start) {
			$start = $this->getStartDate();
		}
		if (!$finish) {
			$finish = $this->getFinishDate();
		}

		$holidays = array();
		foreach ($this as $name => $value) {
			if ($finish->format('Ymd') < $name) {
				break;
			} else if ($start->format('Ymd') <= $name) {
				$holidays[$name] = $value;
			}
		}
		return $holidays;
	}

	/**
	 * ICSカレンダーをパースしてルール配列に格納
	 *
	 * @access protected
	 * @abstract
	 */
	abstract protected function setRules ();

	/**
	 * ルール配列を解釈して、祝日カレンダーを生成
	 *
	 * @access protected
	 * @abstract
	 */
	abstract protected function setHolidays ();

	/**
	 * カレンダーのURLを返す
	 *
	 * @access protected
	 * @return BSURL カレンダーのURL
	 * @abstract
	 */
	abstract protected function getURL ();
}

/* vim:set tabstop=4 ai: */
?>