<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize
 */

/**
 * シリアライズされたキャッシュ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSSerializeHandler {
	private $serializer;
	private $storage;
	private $attributes;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSSerializeHandler インスタンス
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
		throw new BSSingletonException(__CLASS__ . 'はコピーできません。');
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access public
	 * @return BSSerializer シリアライザー
	 */
	public function getSerializer () {
		if (!$this->serializer) {
			$this->serializer = BSClassLoader::getInstance()->getObject(
				BS_SERIALIZE_SERIALIZER,
				'Serializer'
			);
			if (!$this->serializer->initialize()) {
				$this->serializer = new BSPHPSerializer;
			}
		}
		return $this->serializer;
	}

	/**
	 * ストレージを返す
	 *
	 * @access public
	 * @return BSSerializeStorage ストレージ
	 */
	public function getStorage () {
		if (!$this->storage) {
			$this->storage = BSClassLoader::getInstance()->getObject(
				BS_SERIALIZE_STORAGE,
				'SerializeStorage'
			);
			if (!$this->storage->initialize()) {
				$this->storage = new BSDefaultSerializeStorage;
				$this->storage->initialize();
			}
		}
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
		return $this->getStorage()->getAttribute($this->getAttributeName($name), $date);
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		return $this->getStorage()->getUpdateDate($this->getAttributeName($name));
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		$serialized = $this->getStorage()->setAttribute($this->getAttributeName($name), $value);
		$message = new BSStringFormat('%sのシリアライズを格納しました。 (%sB)');
		$message[] = $name;
		$message[] = BSNumeric::getBinarySize(strlen($serialized));
		BSController::getInstance()->putLog($message, $this->getStorage());
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		$this->getStorage()->removeAttribute($this->getAttributeName($name));
	}

	/**
	 * 属性名を文字列に正規化する
	 *
	 * @access public
	 * @param mixed $name 属性名に用いる値
	 * @return string 属性名
	 */
	public function getAttributeName ($name) {
		if ($name instanceof BSFile) {
			$file = $name;
			$name = new BSArray(get_class($file));
			$name->merge(explode(DIRECTORY_SEPARATOR, $file->getShortPath()));
			$name->trim();
			return $name->join('.');
		} else if (is_object($name)) {
			return get_class($name);
		}
		return (string)$name;
	}
}

/* vim:set tabstop=4: */
