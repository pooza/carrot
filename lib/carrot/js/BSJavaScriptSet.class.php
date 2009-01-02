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
	private $files = array();
	static private $jssets;

	/**
	 * @access public
	 * @param string $jsset JavaScriptセット名
	 */
	public function __construct ($jsset = 'carrot') {
		$this->name = $jsset;
		$jssets = self::getJavaScriptSets();
		foreach ($jssets[$jsset]['files'] as $name) {
			$this->register($name);
		}
	}

	/**
	 *JavaScriptセット名を返す
	 *
	 * @access public
	 * @return string JavaScriptセット名
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		return join("\n", $this->files);
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
		return BSMediaType::getType('js');
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

	/**
	 * JavaScriptファイルを登録
	 *
	 * @access public
	 * @param string $name JavaScriptファイルの名前
	 */
	public function register ($name) {
		if (isset($this->files[$name])) {
			return;
		}

		$dir = BSController::getInstance()->getDirectory('js');
		$file = $dir->getEntry($name, 'BSJavaScriptFile');
		if (!$file->isReadable()) {
			throw new BSJavaScriptException('%sが読み込めません。', $file);
		}

		$this->files[$name] = $file->getOptimizedContents();
	}

	/**
	 * 全てのJavaScriptセットを返す
	 *
	 * @access private
	 * @return BSArray JavaScriptセットを配列で返す
	 * @static
	 */
	static private function getJavaScriptSets () {
		if (!self::$jssets) {
			self::$jssets = new BSArray;
			require(BSConfigManager::getInstance()->compile('jsset/carrot'));
			self::$jssets->setParameters($config);
			require(BSConfigManager::getInstance()->compile('jsset/application'));
			self::$jssets->setParameters($config);
		}
		return self::$jssets;
	}

	/**
	 * 全てのJavaScriptセットの名前を返す
	 *
	 * @access public
	 * @return BSArray JavaScriptセットの名前を配列で返す
	 * @static
	 */
	static public function getJavaScriptSetNames () {
		$names = clone self::getJavaScriptSets()->getKeys();
		$names[] = 'carrot';
		$names->uniquize();
		$names->sort(BSArray::SORT_VALUE_ASC);
		return $names;
	}
}

/* vim:set tabstop=4: */
