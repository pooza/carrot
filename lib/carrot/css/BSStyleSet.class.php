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
	static private $instances;

	/**
	 * @access private
	 * @param string $name スタイルセット名
	 */
	private function __construct ($name = 'carrot') {
		$this->name = $name;
		$this->files = new BSArray;

		if (isset(self::$instances[$name]['files'])) {
			foreach ((array)self::$instances[$name]['files'] as $file) {
				$this->register($file);
			}
		} else {
			$this->register($name);
		}
	}

	/**
	 * フライウェイトインスタンスを返す
	 *
	 * @access public
	 * @param string $name モジュール名
	 * @static
	 */
	static public function getInstance ($name) {
		if (!self::$instances) {
			self::$instances = new BSArray;
			require(BSConfigManager::getInstance()->compile('styleset/carrot'));
			self::$instances->setParameters($config);
			require(BSConfigManager::getInstance()->compile('styleset/application'));
			self::$instances->setParameters($config);
			self::$instances = self::$instances->getParameters();
		}
		if (!isset(self::$instances[$name]['instance'])) {
			self::$instances[$name]['instance'] = new self($name);
		}
		return self::$instances[$name]['instance'];
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
	 * @param string $name ファイル名
	 */
	public function register ($name) {
		$name = mb_eregi('\.css$', '', $name) . '.css';
		$dirs = new BSArray;
		$dirs[] = BSController::getInstance()->getDirectory('css');
		$dirs[] = BSController::getInstance()->getDirectory('www');
		foreach ($dirs as $dir) {
			if ($file = $dir->getEntry($name, 'BSCSSFile')) {
				if ($file->validate()) {
					$this->files[$name] = $file;
				} else {
					$this->error = $file->getError();
				}
				return;
			}
		}
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
		return BSString::isBlank($this->error);
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
}

/* vim:set tabstop=4: */
