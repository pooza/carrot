<?php
/**
 * @package org.carrot-framework
 * @subpackage validate.validator
 */

/**
 * 時刻バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTimeValidator extends BSValidator {

	/**
	 * 対象文字列から時刻を返す
	 *
	 * fiedlsパラメータが設定されている時はそちらを利用し、対象文字列を無視。
	 *
	 * @access private
	 * @param string $value 対象文字列
	 * @return string 時刻
	 */
	private function getTime ($value) {
		try {
			$date = BSDate::getNow()->clearTime();
			if ($fields = $this['fields']) {
				foreach ($fields as $key => $value) {
					$date[$key] = $this->request[$value];
				}
			} else {
				$date->setDate('2001-01-01 ' . $value);
			}
			if ($date->validate()) {
				return $date->format('H:i:s');
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
		$this['invalid_error'] = '正しい時刻ではありません。';
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
		if (!$date = $this->getTime($value)) {
			$this->error = $this['invalid_error'];
			return false;
		}
		return true;
	}
}

/* vim:set tabstop=4: */
