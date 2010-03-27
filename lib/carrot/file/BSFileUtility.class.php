<?php
/**
 * @package org.carrot-framework
 * @subpackage file
 */

/**
 * ファイルユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFileUtility {

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * 特別なディレクトリを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSDirectory ディレクトリ
	 * @static
	 */
	static public function getDirectory ($name) {
		return BSDirectoryLayout::getInstance()->getDirectory($name);
	}

	/**
	 * 特別なディレクトリのパスを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return string パス
	 * @static
	 */
	static public function getPath ($name) {
		if ($dir = self::getDirectory($name)) {
			return $dir->getPath();
		}
	}

	/**
	 * 特別なディレクトリのURLを返す
	 *
	 * @access public
	 * @param string $name ディレクトリの名前
	 * @return BSHTTPURL URL
	 * @static
	 */
	static public function getURL ($name) {
		if (self::getDirectory($name)) {
			return BSDirectoryLayout::getInstance()->getURL($name);
		}
	}

	/**
	 * 無視対象か？
	 *
	 * @access public
	 * @param string $name ファイル名、またはパス
	 * @return boolean 無視対象ならTrue
	 * @static
	 */
	static public function isIgnoreName ($name) {
		$name = basename($name);
		$config = BSConfigManager::getInstance()->compile('file');
		foreach ($config['ignore_patterns'] as $pattern) {
			if (mb_ereg($pattern, $name)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * 名前がドットから始まるか？
	 *
	 * @access public
	 * @param string $name ファイル名、またはパス
	 * @return boolean ドットから始まるならTrue
	 * @static
	 */
	static public function isDottedName ($name) {
		return mb_ereg('^\\.', basename($name));
	}

	/**
	 * 添付ファイルを検索
	 *
	 * @access public
	 * @param BSDirectory $dir ディレクトリ
	 * @param string $basename ベースファイル名
	 * @return BSFile 添付ファイル
	 * @static
	 */
	static public function searchAttachment (BSDirectory $dir, $basename) {
		foreach (BSMIMEType::getAttachableTypes() as $suffix => $type) {
			if ($file = $dir->getEntry($basename . $suffix)) {
				return $file;
			}
		}
	}

	/**
	 * 一時ファイルを生成して返す
	 *
	 * @access public
	 * @param string $suffix 拡張子
	 * @param string $class クラス名
	 * @return BSFile 一時ファイル
	 * @static
	 */
	static public function getTemporaryFile ($suffix = null, $class = 'BSFile') {
		$name = BSUtility::getUniqueID() . '.' . ltrim($suffix, '.');
		if (!$file = BSFileUtility::getDirectory('tmp')->createEntry($name, $class)) {
			throw new BSFileException('一時ファイルが生成できません。');
		}
		return $file;
	}
}

/* vim:set tabstop=4: */
