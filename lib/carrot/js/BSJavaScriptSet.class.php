<?php
/**
 * @package org.carrot-framework
 * @subpackage js
 */

/**
 * JavaScriptセット
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSJavaScriptSet implements BSTextRenderer {
	private $name;
	private $error;
	private $contentFragments;
	static private $instances;

	/**
	 * @access private
	 * @param string $name JavaScriptセット名
	 */
	private function __construct ($name = 'carrot') {
		$this->name = $name;
		$this->contentFragments = new BSArray;
		foreach ((array)self::$instances[$name]['files'] as $file) {
			$this->register($file);
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
			require(BSConfigManager::getInstance()->compile('jsset/carrot'));
			self::$instances->setParameters($config);
			require(BSConfigManager::getInstance()->compile('jsset/application'));
			self::$instances->setParameters($config);
			self::$instances = self::$instances->getParameters();
		}
		if (!isset(self::$instances[$name]['instance'])) {
			self::$instances[$name]['instance'] = new self($name);
		}
		return self::$instances[$name]['instance'];
	}

	/**
	 * JavaScriptセット名を返す
	 *
	 * @access public
	 * @return string JavaScriptセット名
	 */
	public function getName () {
		return $this->name;
	}


	/**
	 * JavaScriptファイルを登録
	 *
	 * @access public
	 * @param string $name JavaScriptファイルの名前
	 */
	public function register ($name) {
		$name = mb_eregi_replace('\\.js$', '', $name) . '.js';
		$dirs = new BSArray;
		$dirs[] = BSController::getInstance()->getDirectory('js');
		$dirs[] = BSController::getInstance()->getDirectory('www');
		foreach ($dirs as $dir) {
			if ($file = $dir->getEntry($name, 'BSJavaScriptFile')) {
				if ($file->isReadable()) {
					$this->contentFragments[$name] = $file->getOptimizedContents();
				} else {
					$this->error = $file . 'が読み込めません。';
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
		return $this->contentFragments->join("\n");
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
		return BSMIMEType::getType('js');
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @return string PHPのエンコード名
	 */
	public function getEncoding () {
		return 'utf-8';
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
