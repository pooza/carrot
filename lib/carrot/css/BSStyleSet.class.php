<?php
/**
 * @package org.carrot-framework
 * @subpackage css
 */

/**
 * スタイルセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSStyleSet implements BSTextRenderer {
	private $name;
	private $files;
	private $contents;
	private $error;
	private $parser;
	static private $stylesets;

	/**
	 * @access public
	 * @param string $name スタイルセット名
	 */
	public function __construct ($name = 'carrot') {
		$this->name = $name;
		$this->files = new BSArray;

		$stylesets = self::getStyleSets();
		$dir = BSController::getInstance()->getDirectory('css');
		if (isset($stylesets[$name]['files'])) {
			$files = $stylesets[$name]['files'];
			foreach ($files as $file) {
				$this->register($dir->getEntry($file, 'BSCSSFile'));
			}
		} else if ($file = $dir->getEntry($name, 'BSCSSFile')) {
			$this->register($file);
		}
	}

	/**
	 * スタイルセット名を返す
	 *
	 * @access public
	 * @return string スタイルセット名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * パーサーを返す
	 *
	 * @access public
	 * @return HTML_CSS パーサー
	 */
	public function getParser () {
		if (!$this->parser) {
			BSUtility::includeFile('pear/HTML/CSS.php');
			$this->parser = new HTML_CSS;
			$this->parser->setCharset('utf-8');
		}
		return $this->parser;
	}

	/**
	 * 登録
	 *
	 * @access public
	 * @param BSCSSFile $file ファイル
	 */
	public function register (BSCSSFile $file) {
		if (!$file->validate()) {
			$this->error = $file->getError();
		}
		$this->files[] = $file;
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		if (!$this->contents) {
			foreach ($this->files as $file) {
				$this->getParser()->parseString($file->getOptimizedContents());
			}
			$this->contents = $this->getParser()->toString();
		}
		return $this->contents;
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
		return BSMIMEType::getType('css');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return $this->getParser()->getCharset();
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		return !BSString::isBlank($this->getContents());
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}

	/**
	 * 全てのスタイルセットを返す
	 *
	 * @access private
	 * @return string[][] スタイルセットを配列で返す
	 * @static
	 */
	static private function getStyleSets () {
		if (!self::$stylesets) {
			self::$stylesets = new BSArray;
			require(BSConfigManager::getInstance()->compile('styleset/carrot'));
			self::$stylesets->setParameters($config);
			require(BSConfigManager::getInstance()->compile('styleset/application'));
			self::$stylesets->setParameters($config);
		}
		return self::$stylesets;
	}

	/**
	 * 全てのスタイルセットの名前を返す
	 *
	 * @access public
	 * @return BSArray スタイルセットの名前を配列で返す
	 * @static
	 */
	static public function getStyleSetNames () {
		$names = clone self::getStyleSets()->getKeys();
		$names[] = 'carrot';
		$names->uniquize();
		$names->sort(BSArray::SORT_VALUE_ASC);
		return $names;
	}
}

/* vim:set tabstop=4: */
