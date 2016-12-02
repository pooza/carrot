<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage net.mime.header.date
 */

/**
 * Expiresヘッダ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSExpiresMIMEHeader extends BSDateMIMEHeader {
	protected $name = 'Expires';

	/**
	 * キャッシュ可能か？
	 *
	 * @access public
	 * @return boolean キャッシュ可能ならばTrue
	 */
	public function isCacheable () {
		return false;
	}
}

/* vim:set tabstop=4: */
