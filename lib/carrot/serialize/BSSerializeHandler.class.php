<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize
 */

/**
 * シリアライズされたキャッシュ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSSerializeHandler {
	private $serializer;
	private $storage;
	private $attributes;

	/**
	 * @access public
	 */
	public function __construct (BSSerializeStorage $storage = null, BSSerializer $serializer = null) {
		$classes = BSClassLoader::getInstance();

		if (!$serializer) {
			$serializer = $classes->getObject(BS_SERIALIZE_SERIALIZER, 'Serializer');
		}
		$this->serializer = $serializer;
		if (!$this->serializer->initialize()) {
			throw new BSConfigException($serializer . 'が初期化できません。');
		}

		if (!$storage) {
			$storage = $classes->getObject(BS_SERIALIZE_STORAGE, 'SerializeStorage');
		}
		$this->storage = $storage;
		if (!$this->storage->initialize()) {
			throw new BSConfigException($storage . 'が初期化できません。');
		}
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access public
	 * @return BSSerializer シリアライザー
	 */
	public function getSerializer () {
		return $this->serializer;
	}

	/**
	 * ストレージを返す
	 *
	 * @access public
	 * @return BSSerializeStorage ストレージ
	 */
	public function getStorage () {
		return $this->storage;
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
		return $this->storage->getAttribute($this->createKey($name), $date);
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		return $this->storage->getUpdateDate($this->createKey($name));
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		if (is_array($value) || ($value instanceof BSParameterHolder)) {
			$value = new BSArray($value);
			$value = $value->decode();
		}
		$this->storage->setAttribute($this->createKey($name), $value);
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		$this->storage->removeAttribute($this->createKey($name));
	}

	/**
	 * シリアライズのダイジェストを返す
	 *
	 * @access public
	 * @param mixed $name 属性名に用いる値
	 * @return string 属性名
	 */
	public function createKey ($name) {
		if ($name instanceof BSSerializable) {
			return $name->digest();
		} else if (is_object($name)) {
			return BSCrypt::digest(get_class($name));
		}
		return (string)$name;
	}
}

/* vim:set tabstop=4: */
