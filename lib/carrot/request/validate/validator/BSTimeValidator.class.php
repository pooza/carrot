<?php
/**
 * @package org.carrot-framework
 * @subpackage request.validate.validator
 */

/**
 * 時刻バリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTimeValidator extends BSValidator {

	/**
	 * 対象文字列から時刻を返す
	 *
	 * fiedlsパラメータが設定されている時はそちらを利用し、対象文字列を無視。
	 *
	 * @access protected
	 * @param string $value 対象文字列
	 * @return string 時刻
	 */
	protected function getTime ($value) {
		try {
			$date = BSDate::getNow()->clearTime();
			if ($fields = $this['fields']) {
				foreach ($fields as $key => $value) {
					$date[$key] = $this->request[$value];
				}
			} else {
				$date->setDate($value);
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
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = array()) {
		$this['fields'] = array();
		$this['invalid_error'] = '正しい時刻ではありません。';
		return parent::initialize($params);
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
