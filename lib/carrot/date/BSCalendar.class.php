<?php
/**
 * @package org.carrot-framework
 * @subpackage date
 */

/**
 * カレンダー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSCalendar implements IteratorAggregate {
	private $weeks = array();
	private $start;
	private $end;

	/**
	 * @access public
	 * @param BSDate $start 開始日
	 * @param BSDate $end 終了日
	 */
	public function __construct (BSDate $start, BSDate $end) {
		if ($start->isAgo($end)) {
			$this->start = $start;
			$this->end = $end;
		} else {
			$this->start = $end;
			$this->end = $start;
		}
		$this->initialize();
	}

	/**
	 * 初期化
	 *
	 * 日付を書き込む
	 *
	 * @access private
	 */
	private function initialize () {
		$date = clone $this->start;
		$date->setAttribute('day', '-' . ($date->format('N') - 1));
		$end = clone $this->end;
		$end->setAttribute('day', '+' . (7 - $end->format('N')));
		while ($date->getTimeStamp() <= $end->getTimeStamp()) {
			$values = array(
				'day' => $date->getAttribute('day'),
				'weekday' => $date->format('ww'),
			);
			if ($date->isAgo($this->start) || $this->end->isAgo($date)) {
				$values['disabled'] = true;
			} else {
				if ($date->isToday($this->start)) {
					$values['year'] = $date->getAttribute('year');
					$values['month'] = $date->getAttribute('month');
				} else if ($date->getAttribute('day') == 1) {
					$values['month'] = $date->getAttribute('month');
					if ($date->getAttribute('month') == 1) {
						$values['year'] = $date->getAttribute('year');
					}
				}
				if ($date->isHoliday()) {
					$values['holiday'] = true;
					$values['holiday_name'] = $date->getHolidayName();
				}
			}
			$this->weeks[$date->format('Y-W')][$date->format('Y-m-d')] = $values;
			$date->setAttribute('day', '+1');
		}

		//年をまたがる対応
		foreach ($this->weeks as $key => $week) {
			$ym = explode('-', $key);
			$year = $ym[0];
			if (isset($yearPrev) && ($year != $yearPrev)) {
				$this->weeks[$key] = $weekPrev + $week;
				unset($this->weeks[$keyPrev]);
			}
			$keyPrev = $key;
			$weekPrev = $week;
			$yearPrev = $year;
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
	 * カレンダーに値を書き込む
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @param string $name 値の名前
	 * @param mixed $value 値
	 */
	public function setValue (BSDate $date, $name, $value) {
		$datekey = $date->format('Y-m-d');
		foreach ($this->weeks as &$week) {
			foreach ($week as $key => &$day) {
				if ($datekey == $key) {
					$day[$name][] = $value;
					return;
				}
			}
		}
	}

	/**
	 * カレンダーに値を書き込む
	 *
	 * @access public
	 * @param string $name 値の名前
	 * @param mixed[] $values 値の連想配列
	 */
	public function setValues ($name, $values) {
		foreach ($this->weeks as &$week) {
			foreach ($week as $key => &$day) {
				if (isset($values[$key])) {
					$day[$name] = $values[$key];
				}
			}
		}
	}

	/**
	 * 特定の曜日を定休日に
	 *
	 * @access public
	 * @param integer[] $weekdays 曜日
	 */
	public function setRegularHolidays ($weekdays) {
		if (!BSArray::isArray($weekdays)) {
			$weekdays = array($weekdays);
		}

		$days = array();
		$date = clone $this->start;
		while ($date <= $this->end) {
			if (in_array($date->getWeekday(), $weekdays)) {
				$days[$date->format('Y-m-d')] = true;
			}
			$date->setAttribute('day', '+1');
		}
		$this->setValues('regular_holiday', $days);
	}

	/**
	 * 特定の曜日を定休日に
	 *
	 * setRegularHolidaysのエイリアス
	 *
	 * @access public
	 * @param integer[] $weekday 曜日
	 * @final
	 */
	final public function setRegularHoliday ($weekday) {
		$this->setRegularHolidays($weekday);
	}

	/**
	 * 指定した日付の情報を返す
	 *
	 * @access public
	 * @param BSDate $date 日付
	 * @return mixed[] 情報
	 */
	public function getDay (BSDate $date) {
		foreach ($this->weeks as $week) {
			foreach ($week as $key => $day) {
				if ($key == $date->format('Y-m-d')) {
					return $day;
				}
			}
		}
	}

	/**
	 * 全ての日付を返す
	 *
	 * @access public
	 * @return mixed[][] 全ての日付の情報
	 */
	public function getDays () {
		$days = array();
		foreach ($this->weeks as $week) {
			foreach ($week as $key => $day) {
				$days[] = $day;
			}
		}
		return $days;
	}

	/**
	 * 全ての日付を週ごとに区切って返す
	 *
	 * @access public
	 * @return mixed[][][] 全ての日付の情報
	 */
	public function getWeeks () {
		return $this->weeks;
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		$dates = new BSArray;
		$date = clone $this->start;
		do {
			$dates[] = clone $date;
			$date->setAttribute('day', '+1');
		} while ($date->getTimeStamp() <= $this->end->getTimeStamp());
		return new BSIterator($dates);
	}
}

/* vim:set tabstop=4 ai: */
?>