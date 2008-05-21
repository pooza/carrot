<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage serialize
 */

/**
 * シリアライズされたキャッシュ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSSerializeHandler extends BSList {
	private $engine;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		if (extension_loaded('json')) {
			$this->engine = new BSJSONSerializer;
		} else {
			$this->engine = new BSPHPSerializer;
		}
		$this->getDirectory()->setDefaultSuffix($this->engine->getSuffix());
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
			self::$instance = new BSSerializeHandler();
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * シリアライザーを返す
	 *
	 * @access private
	 * @param BSSerializer シリアライザー
	 */
	private function getEngine () {
		return $this->engine;
	}

	/**
	 * ディレクトリを返す
	 *
	 * @access private
	 * @param BSDictionary ディレクトリ
	 */
	private function getDirectory () {
		return BSController::getInstance()->getDirectory('serialized');
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 */
	public function setAttribute ($name, $value) {
		$file = $this->getDirectory()->createEntry($name);
		$file->setMode(0666);
		$file->setContents($this->getEngine()->encode($value));
		$this->attributes[$name] = $value;
		BSLog::put($name . 'をシリアライズしました。');
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
	 * @param BSDate $date 比較する日付 - この日付より古い属性値は破棄する
	 * @return mixed 属性値
	 */
	public function getAttribute ($name, BSDate $date = null) {
		if (!isset($this->attributes[$name])) {
			$this->attributes[$name] = null;

			if (!$file = $this->getDirectory()->getEntry($name)) {
				return null;
			} else if (!$file->isReadable()) {
				return null;
			} else if ($date && $file->getUpdateDate()->isAgo($date)) {
				return null;
			}
			$this->attributes[$name] = $this->getEngine()->decode($file->getContents());
		}
		return $this->attributes[$name];
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		foreach ($this->getDirectory() as $file) {
			$this->getAttribute($file->getBaseName());
		}
		return $this->attributes;
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

/* vim:set tabstop=4 ai: */
?>