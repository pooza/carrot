<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request.filter
 */

/**
 * 絵文字リクエストフィルタ
 *
 * 絵文字を取り除く。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSPictogramRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		if (($useragent = $this->request->getUserAgent()) && $useragent->isMobile()) {
			$value = $useragent->getCarrier()->trimPictogram($value);
		}
		return $value;
	}
}

/* vim:set tabstop=4: */
