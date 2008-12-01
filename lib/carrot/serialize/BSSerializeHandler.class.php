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
			self::$instance = new BSSerializeHandler;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access public
	 * @return BSSerializer シリアライザー
	 */
	public function getSerializer () {
		if (!$this->serializer) {
			if (extension_loaded('json')) {
				$this->serializer = new BSJSONSerializer;
			} else {
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
			if (!$type = BSController::getInstance()->getConstant('SERIALIZE_STORAGE_TYPE')) {
				$type = 'default';
			}
			$class = 'BS' . BSString::pascalize($type) . 'SerializeStorage';
			$this->storage = new $class;
			$this->storage->initialize();
		}
		return $this->storage;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		if (is_array($name) || is_object($name)) {
			throw new BSRegisterException('属性名が文字列ではありません。');
		}
		$serialized = $this->getStorage()->setAttribute($name, $value);
		$message = sprintf(
			'%sをシリアライズしました。 (%sB)',
			$name,
			BSNumeric::getBinarySize(strlen($serialized))
		);
		BSController::getInstance()->putLog($message, get_class($this));
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		$this->getStorage()->removeAttribute($name);
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
		return $this->getStorage()->getAttribute($name, $date);
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		return $this->getStorage()->getUpdateDate($name);
	}
}

/* vim:set tabstop=4 ai: */
?>