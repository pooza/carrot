<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage date
 */

/**
 * 日付
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSDate.class.php 323 2007-05-15 11:51:34Z pooza $
 */
class BSDate {
	const SUN = 0;
	const MON = 1;
	const TUE = 2;
	const WED = 3;
	const THU = 4;
	const FRI = 5;
	const SAT = 6;
	private $attributes = array();
	private $timestamp;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $str 日付文字列
	 */
	public function __construct ($str = null) {
		if ($str) {
			$this->setDate($str);
		}
	}

	/**
	 * 日付設定
	 *
	 * @access public
	 * @param string $str 日付文字列
	 */
	public function setDate ($str) {
		if ($time = strtotime($str)) {
			return $this->setTimestamp($time);
		}
	
		$str = preg_replace('/[^0-9]+/', '', $str);
		$this->setAttribute('year', substr($str, 0, 4));
		$this->setAttribute('month', substr($str, 4, 2));
		$this->setAttribute('day', substr($str, 6, 2));
		$this->setAttribute('hour', substr($str, 8, 2));
		$this->setAttribute('minute', substr($str, 10, 2));
		$this->setAttribute('second', substr($str, 12, 2));

		if (!$this->validate()) {
			throw new BSDateException('%sは正しくない日付です。', $this);
		}
	}

	/**
	 * 年月日による日付設定
	 *
	 * @access public
	 * @param integer $year 年
	 * @param integer $month 月
	 * @param integer $day 日
	 */
	public function setYMD ($year, $month, $day = 1) {
		$this->setAttribute('year', $year);
		$this->setAttribute('month', $month);
		$this->setAttribute('day', $day);

		if (!$this->validate()) {
			throw new BSDateException('%sは正しくない日付です。', $this);
		}
	}

	/**
	 * UNIXタイムスタンプを返す
	 *
	 * @access public
	 * @return integer UNIXタイムスタンプ
	 */
	public function getTimestamp () {
		if (!$this->timestamp) {
			$this->timestamp = mktime(
				$this->getAttribute('hour'),
				$this->getAttribute('minute'),
				$this->getAttribute('second'),
				$this->getAttribute('month'),
				$this->getAttribute('day'),
				$this->getAttribute('year')
			);
		}
		return $this->timestamp;
	}

	/**
	 * UNIXタイムスタンプを設定
	 *
	 * @access public
	 * @param integer $timestamp UNIXタイムスタンプ
	 */
	public function setTimestamp ($timestamp) {
		$this->setAttribute('year', date('Y', $timestamp));
		$this->setAttribute('month', date('m', $timestamp));
		$this->setAttribute('day', date('d', $timestamp));
		$this->setAttribute('hour', date('H', $timestamp));
		$this->setAttribute('minute', date('i', $timestamp));
		$this->setAttribute('second', date('s', $timestamp));
		$this->timestamp = $timestamp;

		if (!$this->validate()) {
			throw new BSDateException('"%s"は正しくないタイムスタンプです。', $timestamp);
		}
	}

	/**
	 * 現在日付に設定
	 *
	 * @access public
	 */
	public function setNow () {
		$this->setTimestamp(time());
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed 属性
	 */
	public function getAttribute ($name) {
		if (isset($this->attributes[$name])) {
			return $this->attributes[$name];
		}
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		return $this->attributes;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param integer $value 値
	 */
	public function setAttribute ($name, $value) {
		if (preg_match('/^[\-+]/', $value)) {
			$items = array('hour', 'minute', 'second', 'month', 'day', 'year');
			foreach ($items as $item) {
				$$item = $this->getAttribute($item);
				if ($item == $name) {
					$$item += (int)$value;
				}
			}
			$this->setTimestamp(mktime($hour, $minute, $second, $month, $day, $year));
		} else {
			$this->attributes[$name] = (int)$value;
			$this->timestamp = null;
		}
	}

	/**
	 * 日付の妥当性をチェック
	 *
	 * @access private
	 * @return boolean 妥当な日付ならtrue
	 */
	private function validate () {
		return checkdate(
			$this->getAttribute('month'),
			$this->getAttribute('day'),
			$this->getAttribute('year')
		) && ($this->getTimestamp() !== false);
	}

	/**
	 * 指定日付よりも過去か？
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return boolean 過去日付ならtrue
	 */
	public function isAgo ($now = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (!$now) {
			$now = self::getNow();
		}
		return ($this->getTimestamp() < $now->getTimestamp());
	}

	/**
	 * 今日か？
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return boolean 今日の日付ならtrue
	 */
	public function isToday ($now = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (!$now) {
			$now = self::getNow();
		}
		return ($this->format('Ymd') == $now->format('Ymd'));
	}

	/**
	 * 年数（年齢）を返す
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return integer 年数
	 */
	public function getAge ($now = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (!$now) {
			$now = self::getNow();
		}

		$age = $now->getAttribute('year') - $this->getAttribute('year');
		if ($now->getAttribute('month') < $this->getAttribute('month')) {
			$age --;
		} else if (($now->getAttribute('month') == $this->getAttribute('month'))
			&& ($now->getAttribute('day') < $this->getAttribute('day'))) {
			$age --;
		}
		return $age;
	}

	/**
	 * 月末日付を返す
	 *
	 * @access public
	 * @return BSDate 月末日付
	 */
	public function getLastDateOfMonth () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		return new BSDate($this->format('Ymt'));
	}

	/**
	 * 週末日付を返す
	 *
	 * @access public
	 * @param integer $weekday 曜日
	 * @return BSDate 週末日付
	 */
	public function getLastDateOfWeek ($weekday = self::SUN) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		} else if (($weekday < self::SUN) || (self::SAT < $weekday)) {
			throw new BSDateException('曜日が正しくありません。');
		}

		$date = clone $this;
		$date->setAttribute('hour', 0);
		$date->setAttribute('minute', 0);
		$date->setAttribute('second', 0);
		while ($date->getWeekday() != $weekday) {
			$date->setAttribute('day', '+1');
		}
		return $date;
	}

	/**
	 * うるう年か否か
	 *
	 * @access public
	 * @return boolean うるう年ならtrue
	 */
	public function isLeapYear () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		$date = new BSDate($this->format('Y') . '0201');
		return ($date->getLastDateOfMonth()->getAttribute('day') == 29);
	}

	/**
	 * 曜日を返す
	 *
	 * @access public
	 * @return integer 曜日
	 */
	public function getWeekday () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		return date('w', $this->getTimestamp());
	}

	/**
	 * 書式化した日付を返す
	 *
	 * @access public
	 * @param string $format 書式
	 * @return string 書式化された日付文字列
	 */
	public function format ($format = 'Y/m/d H:i:s') {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (preg_match('/ww/', $format)) {
			$weekdays = array('日', '月', '火', '水', '木', '金', '土');
			$format = str_replace('ww', $weekdays[$this->getWeekday()], $format);
		}
		return date($format, $this->getTimestamp());
	}

	/**
	 * 現在日付を書式化し、文字列で返す
	 *
	 * @access public
	 * @param string $format 書式
	 * @return mixed 書式化された現在日付文字列、書式未指定の場合はBSDateオブジェクト
	 * @static
	 */
	public static function getNow ($format = null) {
		$date = new BSDate();
		$date->setNow();

		if ($format) {
			return $date->format($format);
		} else {
			return $date;
		}
	}

	/**
	 * 月の配列を返す
	 *
	 * @access public
	 * @return string[] 月の配列
	 * @static
	 */
	public static function getMonths () {
		return range(1, 12);
	}

	/**
	 * 日付の配列を返す
	 *
	 * @access public
	 * @return string[] 日付の配列
	 * @static
	 */
	public static function getDays () {
		return range(1, 31);
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf(
			'日付 "%04d-%02d-%02d"',
			$this->getAttribute('year'),
			$this->getAttribute('month'),
			$this->getAttribute('day')
		);
	}
}

/* vim:set tabstop=4 ai: */
?>