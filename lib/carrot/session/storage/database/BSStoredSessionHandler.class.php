<?php
/**
 * @package org.carrot-framework
 * @subpackage session.storage.database
 */

/**
 * セッションテーブル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSStoredSessionHandler extends BSTableHandler {

	/**
	 * レコード追加可能か？
	 *
	 * @access protected
	 * @return boolean レコード追加可能ならTrue
	 */
	protected function isInsertable () {
		return true;
	}

	/**
	 * テーブル名を返す
	 *
	 * @access public
	 * @return string テーブル名
	 */
	public function getName () {
		return BSSessionStorage::TABLE_NAME;
	}

	/**
	 * セッションを開く
	 *
	 * 実際には何もしない
	 *
	 * @access public
	 * @param string $path セッション保存ディレクトリへのパス
	 * @param string $name セッション名
	 * @return boolean 処理の成否
	 */
	public function open ($path, $name) {
		return true;
	}

	/**
	 * セッションを閉じる
	 *
	 * 実際には何もしない
	 *
	 * @access public
	 * @return boolean 処理の成否
	 */
	public function close () {
		return true;
	}

	/**
	 * 古いセッションを削除
	 *
	 * PHPから不定期（低確率）にコールバックされる
	 *
	 * @access public
	 * @param integer $lifetime セッションの寿命（秒数）
	 * @return boolean 処理の成否
	 */
	public function clean ($lifetime) {
		$expire = new BSDate;
		$expire->setTimeStamp(BSDate::getNow()->getTimeStamp() - $lifetime);

		foreach ($this as $record) {
			if ($record->getUpdateDate()->isAgo($expire)) {
				$record->delete();
			}
		}

		return true;
	}

	/**
	 * セッションを返す
	 *
	 * $hoge = $_SESSION['hoge']; の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @return string シリアライズされたセッション
	 */
	public function getAttribute ($name) {
		if ($record = $this->getRecord($name)) {
			return $record->getAttribute('data');
		}
	}

	/**
	 * セッションを設定
	 *
	 * $_SESSION['hoge'] = $hoge; の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @param string $value シリアライズされたセッション
	 * @return boolean 処理の成否
	 */
	public function setAttribute ($name, $value) {
		if ($record = $this->getRecord($name)) {
			$values = array(
				'data' => $value,
				'update_date' => BSDate::getNow('Y-m-d H:i:s'),
			);
			$record->update($values);
		} else {
			$values = array(
				'id' => $name,
				'data' => $value,
				'update_date' => BSDate::getNow('Y-m-d H:i:s'),
			);
			$this->createRecord($values);
		}
		return true;
	}

	/**
	 * セッションを削除
	 *
	 * unset($_SESSION['hoge']); の際にコールバックされる
	 *
	 * @access public
	 * @param string $name セッション名
	 * @return boolean 処理の成否
	 */
	public function removeAttribute ($name) {
		if ($record = $this->getRecord($name)) {
			$record->delete();
		}
		return true;
	}

	/**
	 * データベースを返す
	 *
	 * @access public
	 * @return BSDatabase データベース
	 */
	public function getDatabase () {
		foreach (array('session', 'default') as $db) {
			if ($db = BSDatabase::getInstance($db)) {
				return $db;
			}
		}
	}
}

/* vim:set tabstop=4 ai: */
?>