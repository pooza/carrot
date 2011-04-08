<?php
/**
 * @package org.carrot-framework
 * @subpackage user.role
 */

/**
 * 管理者ロール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSAdministratorRole implements BSRole {
	protected $networks;
	protected $credentials;
	static protected $instance;
	const CREDENTIAL = 'Admin';

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSAdministratorRole インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getID () {
		return $this->getMailAddress()->getContents();
	}

	/**
	 * メールアドレスを返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return BSMailAddress メールアドレス
	 */
	public function getMailAddress ($language = 'ja') {
		return BSMailAddress::create(BS_ADMIN_EMAIL, self::getName($language));
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 */
	public function getName ($language = 'ja') {
		return BSController::getInstance()->getAttribute('app_name_' . $language) . ' 管理者';
	}

	/**
	 * JabberIDを返す
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 */
	public function getJabberID () {
		if (!BSString::isBlank(BS_ADMIN_JID)) {
			return new BSJabberID(BS_ADMIN_JID);
		}
	}

	/**
	 * 許可されたネットワークを返す
	 *
	 * @access public
	 * @return BSArray 許可されたネットワークの配列
	 */
	public function getAllowedNetworks () {
		if (!$this->networks) {
			$this->networks = new BSArray;
			if (BSString::isBlank(BS_ADMIN_NETWORKS)) {
				$this->networks[] = new BSNetwork('0.0.0.0/0');
			} else {
				$this->networks[] = new BSNetwork('127.0.0.1/32');
				foreach (BSString::explode(',', BS_ADMIN_NETWORKS) as $network) {
					$this->networks[] = new BSNetwork($network);
				}
			}
		}
		return $this->networks;
	}

	/**
	 * ユーザーIDを返す
	 *
	 * @access public
	 * @return string ユーザーID
	 */
	public function getUserID () {
		return $this->getMailAddress()->getContents();
	}

	/**
	 * 認証
	 *
	 * @access public
	 * @param string $password パスワード
	 * @return boolean 正しいユーザーならTrue
	 */
	public function auth ($password = null) {
		return (!BSString::isBlank(BS_ADMIN_PASSWORD)
			&& !BSString::isBlank($password)
			&& BSCrypt::getInstance()->auth(BS_ADMIN_PASSWORD, $password)
		);
	}

	/**
	 * 認証時に与えられるクレデンシャルを返す
	 *
	 * @access public
	 * @return BSArray クレデンシャルの配列
	 */
	public function getCredentials () {
		if (!$this->credentials) {
			$this->credentials = new BSArray;
			$this->credentials[] = self::CREDENTIAL;
			if (BS_DEBUG) {
				$this->credentials[] = 'Develop';
			}
		}
		return $this->credentials;
	}
}

/* vim:set tabstop=4: */
