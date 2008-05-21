<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage file
 */

/**
 * メディアタイプのリスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSTypeList extends BSList {
	static private $instance;
	private $file;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		// インスタンス化禁止
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSTypeList インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSTypeList;
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
	 * 設定ファイルを返す
	 *
	 * @access public
	 * @return BSFile 設定ファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$file = new BSFile(BS_TYPES_FILE);
			if (!$file->isReadable()) {
				throw new BSFileException('"%s"を開くことが出来ません。', $file->getPath());
			}
			$this->setFile($file);
		}
		return $this->file;
	}

	/**
	 * 設定ファイルを設定する
	 *
	 * @access public
	 * @param BSFile $file 設定ファイル 
	 */
	public function setFile (BSFile $file) {
		$this->file = $file;
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return mixed[] 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = BSController::getInstance()->getAttribute(
				$this->getName(), $this->getFile()->getUpdateDate()
			);
			if (!$this->attributes) {
				foreach ($this->getFile()->getLines() as $line) {
					$line = preg_replace('/#.*$/', '', $line);
					$line = preg_split('/[ \t]+/', $line);
					for ($i = 1 ; $i < count($line) ; $i ++) {
						$this->attributes[$line[$i]] = $line[0];
					}
				}
				ksort($this->attributes);
				BSController::getInstance()->setAttribute(
					$this->getName(), $this->attributes
				);
			}
		}
		return $this->attributes;
	}

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed 属性
	 */
	public function getAttribute ($name) {
		$name = preg_replace('/^\./', '', $name);
		return parent::getAttribute($name);
	}

	/**
	 * 規定のメディアタイプを返す
	 *
	 * @access public
	 * @param string $suffix サフィックス
	 * @return string メディアタイプ
	 * @static
	 */
	static public function getType ($suffix) {
		if (!$type = self::getInstance()->getAttribute($suffix)) {
			$type = 'application/octet-stream';
		}
		return $type;
	}
}

/* vim:set tabstop=4 ai: */
?>