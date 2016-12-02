<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.filter
 */

/**
 * 抽象リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSRequestFilter extends BSFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @abstract
	 */
	abstract protected function convert ($key, $value);

	/**
	 * 配列を対象とするか
	 *
	 * @access protected
	 * @return boolean 配列を対象とするならTrue
	 * @abstract
	 */
	protected function hasArraySupport () {
		return false;
	}

	public function execute () {
		foreach ($this->request->getParameters() as $key => $value) {
			if (!BSString::isBlank($value) && (!is_array($value) || $this->hasArraySupport())) {
				$this->request[$key] = $this->convert($key, $value);
			}
		}
	}
}

/* vim:set tabstop=4: */
