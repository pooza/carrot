<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage translation
 */

/**
 * 辞書ディレクトリ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDictionaryDirectory extends BSDirectory {
	const DEFAULT_ENTRY_CLASS = 'BSDictionaryFile';

	/**
	 * コンストラクタ
	 *
	 * @access public
	 * @param string $path ディレクトリのパス
	 */
	public function __construct ($path = null) {
		if (!$path) {
			$path = BSController::getInstance()->getPath('dictionaries');
		}
		parent::__construct($path);
		$this->setDefaultSuffix('.csv');
	}

	/**
	 * エントリーを返す
	 *
	 * @access public
	 * @param string $name エントリーの名前
	 * @param string $class エントリーのクラス名
	 * @return BSDirectoryEntry 辞書ファイル
	 */
	public function getEntry ($name, $class = self::DEFAULT_ENTRY_CLASS) {
		if (is_file($path = $this->getPath() . '/' . $name)) {
			return new $class($path);
		} else if (is_file($path .= $this->getDefaultSuffix())) {
			return new $class($path);
		}
	}
}

/* vim:set tabstop=4 ai: */
?>