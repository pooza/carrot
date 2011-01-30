<?php
/**
 * @package org.carrot-framework
 * @subpackage service.oauth
 */

/**
 * OAuthコンシューマ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSOAuthConsumer extends BSParameterHolder {

	/**
	 * コンシューマキーを返す
	 *
	 * @access public
	 * @return string コンシューマキー
	 */
	public function getKey () {
		return $this['key'];
	}

	/**
	 * コンシューマキーを設定
	 *
	 * @access public
	 * @return string $key コンシューマキー
	 */
	public function setKey ($key) {
		$this['key'] = $key;
	}

	/**
	 * コンシューマシークレットを返す
	 *
	 * @access public
	 * @return string コンシューマシークレット
	 */
	public function getSecret () {
		return $this['secret'];
	}

	/**
	 * コンシューマシークレットを設定
	 *
	 * @access public
	 * @return string $secret コンシューマシークレット
	 */
	public function setSecret ($secret) {
		$this['secret'] = $secret;
	}
}

/* vim:set tabstop=4: */
