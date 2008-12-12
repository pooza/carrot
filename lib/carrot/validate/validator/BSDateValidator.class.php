<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 日付バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSDateValidator extends BSValidator {

	/**
	 * 対象文字列から日付を返す
	 *
	 * fiedlsパラメータが設定されている時はそちらを利用し、対象文字列を無視。
	 *
	 * @access private
	 * @param string $value 対象文字列
	 * @return BSDate 日付
	 */
	private function getDate ($value) {
		try {
			$date = new BSDate;
			if ($fields = $this['fields']) {
				foreach ($fields as $key => $value) {
					$date[$key] = $this->request[$value];
				}
			} else {
				$date->setDate($value);
			}
			if ($date->validate()) {
				return $date;
			}
		} catch (BSDateException $e) {
		}
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $parameters パラメータ配列
	 */
	public function initialize ($parameters = array()) {
		$this['fields'] = array();
		$this['invalid_error'] = '正しい日付ではありません。';
		$this['today'] = true;
		$this['today_error'] = '当日の日付は選べません。';
		$this['past'] = true;
		$this['past_error'] = '過去の日付は選べません。';
		$this['future'] = true;
		$this['future_error'] = '未来の日付は選べません。';
		return parent::initialize($parameters);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		if (!$date = $this->getDate($value)) {
			$this->error = $this['invalid_error'];
			return false;
		} else if (!$this['today'] && $date->isToday()) {
			$this->error = $this['today_error'];
			return false;
		} else if (!$this['past'] && $date->isPast()) {
			$this->error = $this['past_error'];
			return false;
		} else if (!$this['future'] && !$date->isPast()) {
			$this->error = $this['future_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
