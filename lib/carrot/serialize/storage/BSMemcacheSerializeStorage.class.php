<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage serialize.storage
 */

/**
 * memcacheシリアライズストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMemcacheSerializeStorage implements BSSerializeStorage {
	use BSBasicObject;
	private $server;
	private $serializer;

	/**
	 * @access public
	 * @param BSSerializer $serializer
	 */
	public function __construct (BSSerializer $serializer = null) {
		if (!$serializer) {
			$serializer = $this->loader->createObject(BS_SERIALIZE_SERIALIZER, 'Serializer');
		}
		$this->serializer = $serializer;
	}

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		if ($this->server = BSMemcacheManager::getInstance()->getServer()) {
			 return true;
		}
		return false;
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
			if (!$date || !$entry['update_date']->isPast($date)) {
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
		$values = [
			'update_date' => BSDate::getNow('Y-m-d H:i:s'),
			'contents' => $value,
		];
		$serialized = $this->serializer->encode($values);
		$this->server->set($name, $serialized);
		return $serialized;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		return $this->server->delete($name);
	}

	/**
	 * 属性を全て削除
	 *
	 * @access public
	 * @final
	 */
	final public function clearAttributes () {
		return $this->clear();
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

	private function getEntry ($name) {
		if ($values = $this->server->get($name)) {
			$values = $this->serializer->decode($values);
			$entry = new BSArray($values);
			$entry['update_date'] = BSDate::create($entry['update_date']);
			return $entry;
		}
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return 'memcacheシリアライズストレージ';
	}
}

/* vim:set tabstop=4: */
