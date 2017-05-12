<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.validate.validator
 */

/**
 * URLバリデータ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSURLValidator extends BSValidator {

	/**
	 * 初期化
	 *
	 * @access public
	 * @param string[] $params パラメータ配列
	 */
	public function initialize ($params = []) {
		$this['net_error'] = '正しくありません。';
		$this['schemes'] = ['http', 'https'];
		$this['scheme_error'] = sprintf(
			'スキーム(%s)が正しくありません。',
			join('|', $this['schemes'])
		);
		$this['allow_fullpath'] = false;
		return BSValidator::initialize($params);
	}

	private function getSchemes () {
		return new BSArray($this['schemes']);
	}

	/**
	 * 実行
	 *
	 * @access public
	 * @param mixed $value バリデート対象
	 * @return boolean 妥当な値ならばTrue
	 */
	public function execute ($value) {
		try {
			$pattern = 'https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+';
			if (!$this['allow_fullpath'] && !mb_ereg($pattern, $value)) {
				$this->error = $this['net_error'];
			}
			if (!$url = BSURL::create($value)) {
				$this->error = $this['net_error'];
			}
			if (!$this['allow_fullpath'] && !$this->getSchemes()->isContain($url['scheme'])) {
				$this->error = $this['scheme_error'];
			}
		} catch (Exception $e) {
			$this->error = $this['net_error'];
		}
		return BSString::isBlank($this->error);
	}
}

