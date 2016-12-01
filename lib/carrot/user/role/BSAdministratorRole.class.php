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
	use BSSingleton;
	protected $credentials;
	const CREDENTIAL = 'Admin';

	/**
	 * @access protected
	 */
	protected function __construct () {
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
				$this->credentials[] = 'Debug';
			}
		}
		return $this->credentials;
	}
}

/* vim:set tabstop=4: */
