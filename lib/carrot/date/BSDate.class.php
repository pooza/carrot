<?php
/**
 * @package org.carrot-framework
 * @subpackage date
 */

/**
 * 日付
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDate implements ArrayAccess {
	const MON = 1;
	const TUE = 2;
	const WED = 3;
	const THU = 4;
	const FRI = 5;
	const SAT = 6;
	const SUN = 7;
	private $attributes;
	static private $timezone;
	const GMT = 'gmt';

	/**
	 * @access public
	 * @param string $date 日付文字列
	 */
	public function __construct ($date = null) {
		if (!self::$timezone) {
			if ($timezone = BSController::getInstance()->getConstant('DATE_TIMEZONE')) {
				self::$timezone = $timezone;
				date_default_timezone_set($timezone);
			}
		}

		$this->attributes = new BSArray;
		$this->attributes['timestamp'] = null;
		$this->attributes['has_time'] = false;
		if ($date) {
			$this->setDate($date);
		}
	}

	/**
	 * @access public
	 */
	public function __clone () {
		$this->attributes = clone $this->attributes;
	}

	/**
	 * 日付を設定
	 *
	 * @access public
	 * @param string $date 日付文字列
	 * @return BSDate 適用後の自分自身
	 */
	public function setDate ($date) {
		if ($time = strtotime($date)) {
			$this->setTimestamp($time);
		} else {
			$date = preg_replace('/[^0-9]+/', '', $date);
			$this['year'] = substr($date, 0, 4);
			$this['month'] = substr($date, 4, 2);
			$this['day'] = substr($date, 6, 2);
			$this['hour'] = substr($date, 8, 2);
			$this['minute'] = substr($date, 10, 2);
			$this['second'] = substr($date, 12, 2);
		}

		if ($this->validate()) {
			return $this;
		} else {
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
		if (!$this->attributes['timestamp']) {
			$this->attributes['timestamp'] = mktime(
				$this['hour'], $this['minute'], $this['second'],
				$this['month'], $this['day'], $this['year']
			);
		}
		return $this->attributes['timestamp'];
	}

	/**
	 * UNIXタイムスタンプを設定
	 *
	 * @access public
	 * @param integer $timestamp UNIXタイムスタンプ
	 * @return BSDate 適用後の自分自身
	 */
	public function setTimestamp ($timestamp) {
		$info = getdate($timestamp);
		$this['year'] = $info['year'];
		$this['month'] = $info['mon'];
		$this['day'] = $info['mday'];
		$this['hour'] = $info['hours'];
		$this['minute'] = $info['minutes'];
		$this['second'] = $info['seconds'];
		$this->attributes['timestamp'] = $timestamp;

		if ($this->validate()) {
			return $this;
		} else {
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
	 * 時刻を持つか？
	 *
	 * @access public
	 * @return boolean 時刻を持つならTrue
	 */
	public function hasTime () {
		return $this->attribute['has_time'];
	}

	/**
	 * 時刻を持つかどうかを設定
	 *
	 * @access public
	 * @param boolean $mode 時刻を持つならTrue
	 */
	public function setHasTime ($mode) {
		if ($this->attributes['has_time'] == $mode) {
			return;
		}

		$this->attributes['timestamp'] = null;
		if ($this->attributes['has_time'] = $mode) {
			foreach (array('hour', 'minute', 'second') as $name) {
				if (!$this->attributes->hasAttribute($name)) {
					$this->attributes[$name] = 0;
				}
			}
		} else {
			foreach (array('hour', 'minute', 'second') as $name) {
				$this->attributes->removeAttribute($name);
			}
		}
	}

	/**
	 * 時刻を0:00に設定し、返す
	 *
	 * @access public
	 * @return BSDate 自分自身
	 */
	public function clearTime () {
		$this->setHasTime(false);
		$this->setHasTime(true);
		return $this;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->attributes[$name];
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return BSArray 全ての属性
	 */
	public function getAttributes () {
		// 各属性を再計算
		$this->getTimestamp();
		$this->getWeekday();
		$this->getWeekdayName();
		$this->getGengo();
		$this->getJapaneseYear();

		return $this->attributes;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param integer $value 属性の値、(+|-)で始まる文字列も可。
	 * @return BSDate 適用後の自分自身
	 */
	public function setAttribute ($name, $value) {
		$name = strtolower($name);
		switch ($name) {
			case 'year':
			case 'month':
			case 'day':
				$this->attributes->removeAttribute('weekday');
				$this->attributes->removeAttribute('weekday_name');
				break;
			case 'hour':
			case 'minute':
			case 'second':
				$this->setHasTime(true);
				break;
			default:
				throw new BSDateException('属性名"%s"は正しくありません。', $name);
		}

		if (preg_match('/^[\-+]/', $value)) {
			foreach (array('hour', 'minute', 'second', 'month', 'day', 'year') as $item) {
				$$item = $this->getAttribute($item);
				if ($item == $name) {
					$$item += (int)$value;
				}
			}
			$this->setTimestamp(mktime($hour, $minute, $second, $month, $day, $year));
		} else {
			$this->attributes[$name] = (int)$value;
			$this->attributes['timestamp'] = null;
		}
		return $this;
	}

	/**
	 * 日付の妥当性をチェック
	 *
	 * @access public
	 * @return boolean 妥当な日付ならtrue
	 */
	public function validate () {
		return checkdate($this['month'], $this['day'], $this['year'])
			&& (0 <= $this['hour']) && ($this['hour'] <= 23)
			&& (0 <= $this['minute']) && ($this['minute'] <= 59)
			&& (0 <= $this['second']) && ($this['second'] <= 59)
			&& ($this->getTimestamp() !== false);
	}

	/**
	 * 指定日付よりも過去か？
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return boolean 過去日付ならtrue
	 */
	public function isPast ($now = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (!$now) {
			$now = self::getNow();
		}
		return ($this->getTimestamp() < $now->getTimestamp());
	}

	/**
	 * 指定日付よりも過去か？
	 *
	 * isPastのエイリアス
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return boolean 過去日付ならtrue
	 * @final
	 */
	final public function isAgo ($now = null) {
		return $this->isPast($now);
	}

	/**
	 * 今日か？
	 *
	 * @access public
	 * @param BSDate $now 比較対象の日付
	 * @return boolean 今日の日付ならtrue
	 */
	public function isToday (BSDate $now = null) {
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
	public function getAge (BSDate $now = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if (!$now) {
			$now = self::getNow();
		}

		$age = $now['year'] - $this['year'];
		if ($now['month'] < $this['month']) {
			$age --;
		} else if (($now['month'] == $this['month']) && ($now['day'] < $this['day'])) {
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
		} else if (($weekday < self::MON) || (self::SUN < $weekday)) {
			throw new BSDateException('曜日が正しくありません。');
		}

		$date = clone $this;
		$date->setHasTime(false);
		while ($date->getWeekday() != $weekday) {
			$date['day'] = '+1';
		}
		return $date;
	}

	/**
	 * うるう年か？
	 *
	 * @access public
	 * @return boolean うるう年ならtrue
	 */
	public function isLeapYear () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		return ($this->format('L') == 1);
	}

	/**
	 * 休日ならば、その名前を返す
	 *
	 * @access public
	 * @param string $country 国名
	 * @return string 休日の名前
	 */
	public function getHolidayName ($country = 'ja') {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		$config = array();
		require(BSConfigManager::getInstance()->compile('date/holiday'));
		if (!isset($config[$country])) {
			throw new BSConfigException('国名"%s"の休日が未定義です。', $country);
		}
		$class = $config[$country]['class'];
		$holidays = new $class;
		$holidays->setDate($this);
		return $holidays[$this['day']];
	}

	/**
	 * 休日か？
	 *
	 * @access public
	 * @param string $country 国名
	 * @return boolean 日曜日か祭日ならTrue
	 */
	public function isHoliday ($country = 'ja') {
		return ($this->getWeekday() == self::SUN) || ($this->getHolidayName($country) != null);
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
		if (!$this->attributes->hasAttribute('weekday')) {
			$this->attributes['weekday'] = (int)date('N', $this->getTimestamp());
		}
		return $this->attributes['weekday'];
	}

	/**
	 * 曜日文字列を返す
	 *
	 * @access public
	 * @return string 曜日
	 */
	public function getWeekdayName () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		if (!$this->attributes->hasAttribute('weekday_name')) {
			$weekdays = new BSArray(array(null, '月', '火', '水', '木', '金', '土', '日'));
			$this->attributes['weekday_name'] = $weekdays[$this->getWeekday()];
		}
		return $this->attributes['weekday_name'];
	}

	/**
	 * 元号を返す
	 *
	 * @access public
	 * @return string 元号
	 */
	public function getGengo () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		if (!$this->attributes->hasAttribute('gengo')) {
			$config = array();
			require(BSConfigManager::getInstance()->compile('date/gengo'));
			foreach ($config as $gengo) {
				if ($gengo['start_date'] <= $this->format('Y-m-d')) {
					$this->attributes['gengo'] = $gengo['name'];
					break;
				}
			}
		}
		return $this->attributes['gengo'];
	}

	/**
	 * 和暦年を返す
	 *
	 * @access public
	 * @return integer 和暦年
	 */
	public function getJapaneseYear () {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}
		if (!$this->attributes->hasAttribute('japanese_year')) {
			$config = array();
			require(BSConfigManager::getInstance()->compile('date/gengo'));
			foreach ($config as $gengo => $values) {
				if ($values['start_date'] <= $this->format('Y-m-d')) {
					$start = new BSDate($values['start_date']);
					$year = $this['year'] - $start['year'] + 1;
					$this->attributes['japanese_year'] = $year;
					break;
				}
			}
		}
		return $this->attributes['japanese_year'];
	}

	/**
	 * 書式化した日付を返す
	 *
	 * @access public
	 * @param string $format 書式
	 * @param integer $flag フラグのビット列
	 * @return string 書式化された日付文字列
	 */
	public function format ($format = 'Y/m/d H:i:s', $flag = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		$format = str_replace('ww', $this->getWeekdayName(), $format);

		if (preg_match('/JY/', $format)) {
			$year = $this->getGengo();
			if ($this->getJapaneseYear() == 1) {
				$year .= '元';
			} else {
				$year .= $this->getJapaneseYear();
			}
			$format = str_replace('JY', $year, $format);
		}

		if ($flag & self::GMT) {
			return gmdate($format, $this->getTimestamp());
		} else {
			return date($format, $this->getTimestamp());
		}
	}

	/**
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->setAttribute($key, $value);
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->attributes->removeParameter($key);
	}

	/**
	 * 現在日付を書式化し、文字列で返す
	 *
	 * @access public
	 * @param string $format 書式
	 * @return mixed 書式化された現在日付文字列、書式未指定の場合はBSDateオブジェクト
	 * @static
	 */
	static public function getNow ($format = null) {
		$date = new BSDate;
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
	 * @return integer[] 月の配列
	 * @static
	 */
	static public function getMonths () {
		$months = array();
		foreach (range(1, 12) as $month) {
			$months[$month] = $month;
		}
		return $months;
	}

	/**
	 * 日の配列を返す
	 *
	 * @access public
	 * @return integer[] 日の配列
	 * @static
	 */
	static public function getDays () {
		$days = array();
		foreach (range(1, 31) as $day) {
			$days[$day] = $day;
		}
		return $days;
	}

	/**
	 * 年の配列を返す
	 *
	 * @access public
	 * @return integer[] 年の配列
	 * @static
	 */
	static public function getYears () {
		$years = array();
		foreach (range(BSDate::getNow('Y'), 1900) as $year) {
			$years[$year] = $year;
		}
		return $years;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('日付 "%04d-%02d-%02d"', $this['year'], $this['month'], $this['day']);
	}
}

/* vim:set tabstop=4 ai: */
?>