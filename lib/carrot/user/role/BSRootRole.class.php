<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage user.role
 */

/**
 * rootロール
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSRootRole implements BSRole {
	use BSSingleton;
	private $email;

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
		if (!$this->email) {
			$command = new BSCommandLine('hostname');
			$hostname = $command->getResult()[0];
			$this->email = BSMailAddress::create('root@' . $hostname, $this->getName($language));
		}
		return $this->email;
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 */
	public function getName ($language = 'ja') {
		return 'Charlie Root';
	}

	/**
	 * JabberIDを返す
	 *
	 * @access public
	 * @return BSJabberID JabberID
	 */
	public function getJabberID () {
		return null;
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
	 * このロールは認証には使用しない。
	 *
	 * @access public
	 * @param string $password パスワード
	 * @return boolean 正しいユーザーならTrue
	 */
	public function auth ($password = null) {
		return false;
	}

	/**
	 * 認証時に与えられるクレデンシャルを返す
	 *
	 * @access public
	 * @return BSArray クレデンシャルの配列
	 */
	public function getCredentials () {
		return new BSArray;
	}
}

/* vim:set tabstop=4: */
