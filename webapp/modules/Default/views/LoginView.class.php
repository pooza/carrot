<?php
/**
 * Loginビュー
 *
 * @package jp.co.b-shock.carrot
 * @subpackage Default
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class LoginView extends BSSmartyView {

	/**
	 * HTTPキャッシュ有効か
	 *
	 * @access public
	 * @return boolean 有効ならTrue
	 */
	public function isCacheable () {
		return false;
	}
}

