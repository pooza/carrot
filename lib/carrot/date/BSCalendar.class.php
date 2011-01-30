<?php
/**
 * @package org.carrot-framework
 * @subpackage date
 */

/**
 * カレンダー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSCalendar implements IteratorAggregate {
	private $dates;
	private $start;
	private $end;

	/**
	 * @access public
	 * @param BSDate $start 開始日
	 * @param BSDate $end 終了日
	 */
	public function __construct (BSDate $start, BSDate $end) {
		if ($start->isPast($end)) {
			$this->start = $start;
			$this->end = $end;
		} else {
			$this->start = $end;
			$this->end = $start;
		}

		$date = clone $this->getStartDate();
		while ($date->getTimestamp() <= $this->getEndDate()->getTimestamp()) {
			$values = clone $date->getAttributes();
			$values['date'] = $date->format('Y-m-d');
			if ($date->isToday()) {
				$values['today'] = true;
			}
			$values['holiday'] = $date->isHoliday();
			$values['holiday_name'] = $date->getHolidayName();
			$this->getDates()->setParameter($date->format('Y-m-d'), $values);
			$date['day'] = '+1';
		}
	}

	/**
	 * 開始日を返す
	 *
	 * @access public
	 * @return BSDate 開始日
	 */
	public function getStartDate () {
		return $this->start;
	}

	/**
	 * 終了日を返す
	 *
	 * @access public
	 * @return BSDate 終了日
	 */
	public function getEndDate () {
		return $this->end;
	}

	/**
	 * 指定した日付の情報を返す
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @return mixed[] 情報
	 */
	public function getDate (BSDate $date) {
		$dateKey = $date->format('Y-m-d');
		foreach ($this->getDates() as $key => $values) {
			if ($key == $dateKey) {
				return $values;
			}
		}
	}

	/**
	 * 指定した日付の情報を返す
	 *
	 * getDateのエイリアス
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @return mixed[] 情報
	 * @final
	 */
	final public function getDay (BSDate $date) {
		return $this->getDate($date);
	}

	/**
	 * 全ての日付を返す
	 *
	 * @access public
	 * @return mixed[][] 全ての日付の情報
	 */
	public function getDates () {
		if (!$this->dates) {
			$this->dates = new BSArray;
		}
		return $this->dates;
	}

	/**
	 * 全ての日付を返す
	 *
	 * getDatesのエイリアス
	 *
	 * @access public
	 * @return mixed[][] 全ての日付の情報
	 * @final
	 */
	final public function getDays () {
		return $this->getDates();
	}

	/**
	 * カレンダーに値を書き込む
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @param string $name 値の名前
	 * @param mixed $value 値
	 */
	public function setValue (BSDate $date, $name, $value) {
		$key = $date->format('Y-m-d');
		if ($this->dates[$key]) {
			if (!$this->dates[$key][$name]) {
				$this->dates[$key][$name] = new BSArray;
			}
			$this->dates[$key][$name][] = $value;
		}
	}

	/**
	 * カレンダーから値を削除
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @param string $name 値の名前
	 * @param mixed $value 値
	 */
	public function removeValue (BSDate $date, $name) {
		$key = $date->format('Y-m-d');
		if ($this->dates[$key]) {
			if (!$this->dates[$key][$name]) {
				$this->dates[$key][$name] = new BSArray;
			}
			$this->dates[$key][$name]->clear();
		}
	}

	/**
	 * カレンダーに同種の値をまとめて書き込む
	 *
	 * @access public
	 * @param string $name 値の名前
	 * @param BSArray $values 日付ごとの値
	 */
	public function setValues ($name, BSArray $values) {
		foreach ($values as $key => $value) {
			$this->setValue(BSDate::getInstance($key), $name, $value);
		}
	}

	/**
	 * 全ての日付を週ごとに区切って返す
	 *
	 * @access public
	 * @return BSArray 全ての日付の情報
	 */
	public function getWeeks () {
		$weeks = new BSArray;
		$date = clone $this->getStartDate();
		$date['day'] = '-' . ($date->format('N') - 1);
		$end = clone $this->getEndDate();
		$end['day'] = '+' . (7 - $end->format('N'));

		while ($date->getTimestamp() <= $end->getTimestamp()) {
			if ($date->format('N') == 1) {
				$week = new BSArray;
				$weeks[] = $week;
			}
			if (!$values = $this->getDate($date)) {
				$values = new BSArray($date->getAttributes());
				$values['disabled'] = true;
			}
			$week[] = $values;
			$date['day'] = '+1';
		}
		return $weeks;
	}

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return new BSIterator($this->getDates());
	}
}

/* vim:set tabstop=4: */
