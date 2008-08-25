<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.storage
 */

/**
 * memcacheシリアライズストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSMemcacheSerializeStorage implements BSSerializeStorage {
	private $server;

	/**
	 * 初期化
	 *
	 * @access public
	 */
	public function initialize () {
		if (!extension_loaded('memcache')) {
			throw new BSException('memcacheモジュールが利用できません。');
		}
		if (!$this->getServer()) {
			throw new BSException('memcachedに接続出来ません。');
		}
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access private
	 * @param BSSerializer シリアライザー
	 */
	private function getSerializer () {
		return BSSerializeHandler::getInstance()->getSerializer();
	}

	/**
	 * memcachedサーバを返す
	 *
	 * @access public
	 * @return Memcache memcachedサーバ
	 */
	private function getServer () {
		if (!$this->server) {
			$constants = BSConstantHandler::getInstance();
			$this->server = new Memcache;
			$this->server->pconnect($constants['MEMCACHE_HOST'], $constants['MEMCACHE_PORT']);
		}
		return $this->server;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null) {
		if ($entry = $this->getEntry($name)) {
			if (!$date || !$entry['update_date']->isAgo($date)) {
				return $entry['contents'];
			}
		}
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return string シリアライズされた値
	 */
	public function setAttribute ($name, $value) {
		if (is_object($value)) {
			throw new BSException('オブジェクトはシリアライズ出来ません。');
		}

		$values = array(
			'update_date' => BSDate::getNow('Y-m-d H:i:s'),
			'contents' => $value,
		);
		$serialized = $this->getSerializer()->encode($values);
		$this->getServer()->set($this->getAttributeName($name), $serialized);
		return $serialized;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		return $this->getServer()->delete($this->getAttributeName($name));
	}

	/**
	 * エントリーを返す
	 *
	 * エントリーには、属性の値と更新日が含まれる
	 *
	 * @access private
	 * @param string $name 属性の名前
	 * @return BSArray エントリー
	 */
	private function getEntry ($name) {
		if ($values = $this->getServer()->get($this->getAttributeName($name))) {
			$values = $this->getSerializer()->decode($values);
			$entry = new BSArray;
			$entry['contents'] = $values['contents'];
			$entry['update_date'] = new BSDate($values['update_date']);
			return $entry;
		}
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		if ($entry = $this->getEntry($name)) {
			return $entry['update_date'];
		}
	}

	/**
	 * memcachedでの属性名を返す
	 *
	 * @access protected
	 * @param string $name 属性名
	 * @return string memcachedでの属性名
	 */
	protected function getAttributeName ($name) {
		$name = array(
			BSController::getInstance()->getServerHost()->getName(),
			get_class($this),
			$name
		);
		return join('.', $name);
	}
}

/* vim:set tabstop=4 ai: */
?>