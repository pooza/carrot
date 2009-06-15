<?php
/**
 * @package org.carrot-framework
 * @subpackage file.archive
 */

/**
 * ZIPアーカイブ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSZipArchive extends ZipArchive implements BSrenderer {
	private $file;
	const WITHOUT_DOTED = 1;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->open($this->getFile()->getPath(), self::OVERWRITE);
	}

	/**
	 * @access public
	 */
	public function __destruct () {
		$this->getFile()->delete();
	}

	/**
	 * エントリーを登録
	 *
	 * @access private
	 * @param BSDirectoryEntry $entry エントリー
	 * @param string $prefix エントリー名のプレフィックス
	 * @param integer $flags フラグのビット列
	 *   self::WITHOUT_DOTED ドットファイルを除く
	 */
	public function register (BSDirectoryEntry $entry, $prefix = null, $flags = null) {
		if (($flags & self::WITHOUT_DOTED) && $entry->isDoted()) {
			return;
		}

		if (BSString::isBlank($prefix)) {
			$path = $entry->getName();
		} else {
			$path = $prefix . DIRECTORY_SEPARATOR . $entry->getName();
		}
		if ($entry->isDirectory()) {
			$this->addEmptyDir($path);
			foreach ($entry as $node) {
				$this->register($node, $path, $flags);
			}
		} else {
			$this->addFile($entry->getPath(), $path);
		}
	}

	/**
	 * ファイルを返す
	 *
	 * @access private
	 * @return BSFile ファイル
	 */
	private function getFile () {
		if (!$this->file) {
			$this->file = BSFile::getTemporaryFile('.zip');
		}
		return $this->file;
	}

	/**
	 * 出力内容を返す
	 *
	 * @access public
	 */
	public function getContents () {
		return $this->getFile()->getContents();
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return BSMIMEType::getType('zip');
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return null;
	}
}

/* vim:set tabstop=4: */
