<?php
/**
 * @package org.carrot-framework
 * @subpackage date
 */

/**
 * 日付
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDate implements ArrayAccess, BSAssignable {
	const MON = 1;
	const TUE = 2;
	const WED = 3;
	const THU = 4;
	const FRI = 5;
	const SAT = 6;
	const SUN = 7;
	private $attributes;
	const GMT = 'GMT';
	const TIMESTAMP = 1;
	const NO_INITIALIZE = 2;
	static private $gengos;

	/**
	 * @access private
	 * @param string $date 日付文字列
	 * @param integer $flags フラグのビット列
	 *   self::NO_INITIALIZE 初期化しない
	 *   self::TIMESTAMP タイムスタンプ形式
	 */
	private function __construct ($date, $flags) {
		$this->attributes = new BSArray;
		$this->attributes['timestamp'] = null;
		$this->attributes['has_time'] = false;

		if ($flags & self::NO_INITIALIZE){
			// 何もしない
		} else if (BSString::isBlank($date)) {
			$this->setNow();
		} else if ($flags & self::TIMESTAMP){
			$this->setTimestamp($date);
		} else {
			$this->setDate($date);
		}
	}

	/**
	 * ファクトリインスタンスを返す
	 *
	 * @param string $date 日付文字列
	 * @return BSDate インスタンス
	 * @param integer $flags フラグのビット列
	 *   self::NO_INITIALIZE 初期化しない
	 *   self::TIMESTAMP タイムスタンプ形式
	 * @static
	 */
	static public function getInstance ($date = null, $flags = null) {
		if ($date instanceof BSDate) {
			return $date;
		}

		try {
			$date = new self($date, $flags);
			if (($flags & self::NO_INITIALIZE) || $date->validate()) {
				return $date;
			}
		} catch (BSDateException $e) {
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
			$date = mb_ereg_replace('[^[:digit:]]+', '', $date);
			$this['year'] = (int)substr($date, 0, 4);
			$this['month'] = (int)substr($date, 4, 2);
			$this['day'] = (int)substr($date, 6, 2);
			$this['hour'] = (int)substr($date, 8, 2);
			$this['minute'] = (int)substr($date, 10, 2);
			$this['second'] = (int)substr($date, 12, 2);
		}

		if ($this->validate()) {
			return $this;
		} else {
			throw new BSDateException($this . 'は正しくない日付です。');
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
			throw new BSDateException($timestamp . 'は正しくないタイムスタンプです。');
		}
	}

	/**
	 * 現在日付に設定
	 *
	 * @access public
	 */
	public function setNow () {
		$this->setTimestamp($_SERVER['REQUEST_TIME']);
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
				if (!$this->attributes->hasParameter($name)) {
					$this->attributes[$name] = 0;
				}
			}
		} else {
			foreach (array('hour', 'minute', 'second') as $name) {
				$this->attributes->removeParameter($name);
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

		return clone $this->attributes;
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
		$name = BSString::toLower($name);
		switch ($name) {
			case 'year':
			case 'month':
			case 'day':
				$this->attributes->removeParameter('weekday');
				$this->attributes->removeParameter('weekday_name');
				break;
			case 'hour':
			case 'minute':
			case 'second':
				$this->setHasTime(true);
				break;
			default:
				$message = new BSStringFormat('属性名 "%s"は正しくありません。');
				$message[] = $name;
				throw new BSDateException($message);
		}

		if (($value[0] == '+') || ($value[0] == '-')) {
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
		return (checkdate($this['month'], $this['day'], $this['year'])
			&& (0 <= $this['hour']) && ($this['hour'] <= 23)
			&& (0 <= $this['minute']) && ($this['minute'] <= 59)
			&& (0 <= $this['second']) && ($this['second'] <= 59)
			&& ($this->getTimestamp() !== false)
		);
	}

	/**
	 * 指定日付よりも過去か？
	 *
	 * 配列が与えられたら、その中の最新日付と比較。
	 *
	 * @access public
	 * @param mixed $date 比較対象の日付またはその配列
	 * @return boolean 過去日付ならtrue
	 */
	public function isPast ($date = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		if ($date === null) {
			$date = self::getNow();
		} else if (BSArray::isArray($date)) {
			$date = self::getNewest(new BSArray($date));
		} else if (!($date instanceof BSDate)) {
			if (!$date = BSDate::getInstance($date)) {
				throw new BSDateException('日付が正しくありません。');
			}
		}
		return ($this->getTimestamp() < $date->getTimestamp());
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
		return BSDate::getInstance($this->format('Ymt'));
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

		$config = BSConfigManager::getInstance()->compile('date');
		if (!isset($config['holiday'][$country])) {
			$message = new BSStringFormat('国名 "%s"の休日が未定義です。');
			$message[] = $country;
			throw new BSConfigException($message);
		}
		$holidays = new $config['holiday'][$country]['class'];
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
		return (($this->getWeekday() == self::SUN) || !!$this->getHolidayName($country));
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
		if (!$this->attributes->hasParameter('weekday')) {
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
		if (!$this->attributes->hasParameter('weekday_name')) {
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
		if (!$this->attributes->hasParameter('gengo')) {
			foreach (self::getGengos() as $gengo) {
				if (!$this->isPast($gengo['start_date'])) {
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
		if (!$this->attributes->hasParameter('japanese_year')) {
			foreach (self::getGengos() as $gengo) {
				if (!$this->isPast($gengo['start_date'])) {
					$year = $this['year'] - $gengo['start_date']['year'] + 1;
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
	 * strftime関数とdate関数で処理。
	 *
	 * @access public
	 * @param string $format 書式
	 * @param integer $flags フラグのビット列
	 *   self::GMT GMT時刻で返す。
	 * @return string 書式化された日付文字列
	 */
	public function format ($format = 'Y/m/d H:i:s', $flags = null) {
		if (!$this->validate()) {
			throw new BSDateException('日付が初期化されていません。');
		}

		$date = clone $this;
		if ($flags & self::GMT) {
			$date->setDate(gmdate('Y/m/d H:i:s', $this->getTimestamp()));
		}

		if (BSString::isContain('%', $format)) {
			$format = strftime($format, $date->getTimestamp());
		}
		if (BSString::isContain('ww', $format)) {
			$format = str_replace('ww', $date->getWeekdayName(), $format);
		}
		if (BSString::isContain('JY', $format)) {
			$year = $date->getGengo();
			if ($this->getJapaneseYear() == 1) {
				$year .= '元';
			} else {
				$year .= $date->getJapaneseYear();
			}
			$format = str_replace('JY', $year, $format);
		}
		return date($format, $date->getTimestamp());
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->attributes->hasParameter($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		$this->setAttribute($key, $value);
	}

	/**
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		$this->attributes->removeParameter($key);
	}

	/**
	 * アサインすべき値を返す
	 *
	 * @access public
	 * @return mixed アサインすべき値
	 */
	public function getAssignValue () {
		return $this->format('Y-m-d H:i:s');
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('日付 "%04d-%02d-%02d"', $this['year'], $this['month'], $this['day']);
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
		$date = self::getInstance();
		if (BSString::isBlank($format)) {
			return $date;
		} else {
			return $date->format($format);
		}
	}

	/**
	 * 配列の中から、最も新しい日付を返す
	 *
	 * @access public
	 * @param BSArray 日付の配列
	 * @return BSDate 最も新しい日付
	 * @static
	 */
	static public function getNewest (BSArray $dates) {
		$newest = null;
		foreach ($dates as $date) {
			if (!($date instanceof BSDate)) {
				if (!$date = self::getInstance($date)) {
					throw new BSDateException('日付でない要素が含まれています。');
				}
			}
			if (!$newest || $newest->isPast($date)) {
				$newest = $date;
			}
		}
		return $newest;
	}

	/**
	 * 元号の配列を返す
	 *
	 * @access public
	 * @return BSArray 元号の配列
	 * @static
	 */
	static public function getGengos () {
		if (!self::$gengos) {
			self::$gengos = new BSArray;
			$config = BSConfigManager::getInstance()->compile('date');
			if (!isset($config['gengo'])) {
				throw new BSConfigException('元号が設定されていません。');
			}
			foreach ($config['gengo'] as $gengo) {
				$gengo = new BSArray($gengo);
				$gengo['start_date'] = BSDate::getInstance($gengo['start_date']);
				if (!$gengo['start_date']) {
					continue;
				}
				self::$gengos[$gengo['name']] = $gengo;
			}
		}
		return self::$gengos;
	}
}

/* vim:set tabstop=4: */
