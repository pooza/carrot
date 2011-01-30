<?php
/**
 * @package org.carrot-framework
 * @subpackage serialize.storage
 */

/**
 * 規定シリアライズストレージ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSDefaultSerializeStorage implements BSSerializeStorage {
	private $attributes;

	/**
	 * 初期化
	 *
	 * @access public
	 * @return string 利用可能ならTrue
	 */
	public function initialize () {
		$this->getDirectory()->setDefaultSuffix($this->getSerializer()->getSuffix());
		return $this->getDirectory()->isWritable();
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
	 * ディレクトリを返す
	 *
	 * @access private
	 * @param BSDictionary ディレクトリ
	 */
	private function getDirectory () {
		return BSFileUtility::getDirectory('serialized');
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
		$file = $this->getDirectory()->createEntry($name);
		$file->setMode(0666);
		$file->setContents($serialized = $this->getSerializer()->encode($value));
		$this->attributes[$name] = $value;
		return $serialized;
	}

	/**
	 * 属性を削除
	 *
	 * @access public
	 * @param string $name 属性の名前
	 */
	public function removeAttribute ($name) {
		if ($this->getAttribute($name)) {
			$file = $this->getDirectory()->getEntry($name);
			$file->delete();
			unset($this->attributes[$name]);
		}
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
		if (!isset($this->attributes[$name])) {
			$this->attributes[$name] = null;

			if (!$file = $this->getDirectory()->getEntry($name)) {
				return null;
			} else if (!$file->isReadable()) {
				return null;
			} else if ($date && $file->getUpdateDate()->isPast($date)) {
				return null;
			}
			$this->attributes[$name] = $this->getSerializer()->decode($file->getContents());
		}
		return $this->attributes[$name];
	}

	/**
	 * 属性の更新日を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @return BSDate 更新日
	 */
	public function getUpdateDate ($name) {
		if (!$file = $this->getDirectory()->getEntry($name)) {
			return null;
		}
		return $file->getUpdateDate();
	}
}

/* vim:set tabstop=4: */
