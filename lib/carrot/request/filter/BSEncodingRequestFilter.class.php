<?php
/**
 * @package org.carrot-framework
 * @subpackage request.filter
 */

/**
 * エンコーディング リクエストフィルタ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSEncodingRequestFilter extends BSRequestFilter {

	/**
	 * 変換して返す
	 *
	 * @access protected
	 * @param mixed $key フィールド名
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 */
	protected function convert ($key, $value) {
		return BSString::convertEncoding(
			$value,
			'utf-8',
			$this->request->getUserAgent()->getDefaultEncoding()
		);
	}

	public function execute () {
		if (!ini_get('mbstring.encoding_translation') || $this['force']) {
			parent::execute();
		}
	}
}

/* vim:set tabstop=4: */
