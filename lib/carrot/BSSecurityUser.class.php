<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * BasicSecurityUserとほぼ同等
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSecurityUser extends SecurityUser {
	const CREDENTIAL_NAMESPACE = 'jp/co/b-shock/user/BSSecurityUser/credentials';
	private $credentials = array();

	/**
	 * 初期化
	 *
	 * @access public
	 * @param Context $context Mojaviコンテキスト
	 * @param mixed[] $parameters パラメータ
	 */
	public function initialize ($context, $parameters = null) {
		parent::initialize($context, $parameters);
		$this->credentials = $this->getStorage()->read(self::CREDENTIAL_NAMESPACE);
	}

	/**
	 * シャットダウン
	 *
	 * @access public
	 */
	public function shutdown () {
		$this->getStorage()->write(self::CREDENTIAL_NAMESPACE, $this->credentials);
		parent::shutdown();
	}

	/**
	 * ストレージを返す
	 *
	 * @access private
	 * @return Storage ストレージ
	 */
	private function getStorage () {
		return $this->getContext()->getStorage();
	}

	/**
	 * 全てのクレデンシャルを返す
	 *
	 * @access public
	 * @return string[] 全てのクレデンシャル
	 */
	public function getCredentials () {
		return $this->credentials;
	}

	/**
	 * クレデンシャルを追加する
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function addCredential ($credential) {
		if (!$this->hasCredential($credential)) {
			$this->credentials[$credential] = true;
		}
	}

	/**
	 * クレデンシャルを削除する
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 */
	public function removeCredential ($credential) {
		if ($this->hasCredential($credential)) {
			$this->credentials[$credential] = false;
		}
	}

	/**
	 * 全てのクレデンシャルを削除する
	 *
	 * @access public
	 */
	public function clearCredentials () {
		$this->credentials = array();
	}

	/**
	 * クレデンシャルを持っているか？
	 *
	 * @access public
	 * @param string $credential クレデンシャル
	 * @return boolean 持っていればTrue
	 */
	public function hasCredential ($credential) {
		return isset($this->credentials[$credential]) && $this->credentials[$credential];
	}

	/**
	 * 認証されているか？
	 *
	 * BasicSecurityUserとの互換性の為。クレデンシャルがひとつでもあればTrue。
	 *
	 * @access public
	 * @return boolean 認証されていればTrue
	 */
	public function isAuthenticated () {
		return (0 < count($this->credentials));
	}

	/**
	 * 認証フラグをセット
	 *
	 * BasicSecurityUserとの互換性の為のメソッド。実際には何もしない。
	 *
	 * @access public
	 * @param boolean $authenticated 認証フラグ
	 */
	public function setAuthenticated ($authenticated) {
	}
}
?>