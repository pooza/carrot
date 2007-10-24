<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage date
 */

/**
 * 日本の祝日リスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSJapaneseHolidayList extends BSHolidayList {
	const URL = 'http://homepage.mac.com/ical/.calendars/Japanese32Holidays.ics';

	/**
	 * ICSカレンダーをパースしてルール配列に格納
	 *
	 * @access protected
	 * @todo iCalendar(RFC2445)のMUST要求
	 */
	protected function setRules () {
		foreach (explode("\n", $this->getBody()) as $line) {
			if (!preg_match('/([^:]+):(.+)/', $line, $matches)) {
				continue;
			}
			$key = $matches[1];
			$value = $matches[2];
			if ($value == 'VCALENDAR') {
				continue;
			}

			switch ($key) {
				case 'BEGIN':
					$rule = array();
					break;
				case 'DTSTART;VALUE=DATE':
					$rule['date'] = $value;
					break;
				case 'SUMMARY':
					$rule['name'] = $value;
					break;
				case 'RRULE':
					$date = new BSDate($rule['date']);
					if (preg_match('/BYDAY=([1-5])MO/', $value, $matches)) {
						// ルールを「ハッピーマンデー」に設定
						unset($rule['date']);
						$rule['rule'] = 2;
						$rule['month'] = $date->getAttribute('month');
						$rule['week'] = $matches[1];
					} else {
						// ルールを「毎年日付が変わらない祝日」に設定
						$rule['rule'] = 1;
						$rule['date'] = $date->format('md');
					}
					break;
				case 'END':
					if (isset($rule['rule'])) {
						$this->rules[] = $rule;
					} else {
						// ルールが設定されていない場合は、「毎年日付が変わる祝日」
						$rule['rule'] = 3;
						$this->rules[] = $rule;
					}
					break;
			}
		}
	}

	/**
	 * ルール配列を解釈して、祝日カレンダーを生成
	 *
	 * @access protected
	 * @todo iCalendar(RFC2445)のMUST要求
	 */
	protected function setHolidays () {
		foreach ($this->rules as $rule) {
			switch ($rule['rule']) {
				case 1: //毎年日付が変わらない祝日
					$date = clone $this->getStartDate();
					while ($date->format('Ymd') <= $this->getEndDate()->format('Ymd')) {
						if ($date->format('md') == $rule['date']) {
							$this->attributes[$date->format('Y-m-d')] = $rule['name'];
						}
						$date->setAttribute('day', '+1');
					}
					break;
				case 2: //ハッピーマンデー
					$date = clone $this->getStartDate();
					$date->setAttribute('day', 1);
					while ($date->format('Ymd') <= $this->getEndDate()->format('Ymd')) {
						if ($date->getAttribute('day') == 1) {
							$week = 0;
						}
						if ($date->getWeekday() == BSDate::MON) {
							$week ++;
							if (($this->getStartDate()->format('Ymd') <= $date->format('Ymd'))
								&& ($date->getAttribute('month') == $rule['month'])
								&& ($week == $rule['week'])) {

								$this->attributes[$date->format('Y-m-d')] = $rule['name'];
							}
						}
						$date->setAttribute('day', '+1');
					}
					break;
				case 3: //毎年日付が変わる祝日
					$date = new BSDate($rule['date']);
					if ($date->format('Ymd') < $this->getStartDate()->format('Ymd')) {
						continue;
					} else if ($this->getEndDate()->format('Ymd') < $date->format('Ymd')) {
						continue;
					}

					$this->attributes[$date->format('Y-m-d')] = $rule['name'];
					break;
			}
		}
		ksort($this->attributes);
	}

	/**
	 * カレンダーのURLを返す
	 *
	 * @access protected
	 * @return BSURL カレンダーのURL
	 */
	protected function getURL () {
		if (!$this->url) {
			$this->url = new BSURL(self::URL);
		}
		return $this->url;
	}
}

/* vim:set tabstop=4 ai: */
?>