<?php
/**
 * @package org.carrot-framework
 * @subpackage js
 */

/**
 * JavaScriptファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSJavaScriptFile extends BSFile {

	/**
	 * 内容を最適化して返す
	 *
	 * @access public
	 * @return string 最適化された内容
	 */
	public function getOptimizedContents () {
		$path = $this->getDirectory()->getPath() . DIRECTORY_SEPARATOR . $this->getBaseName();
		$path = str_replace(BSController::getInstance()->getPath('js'), '', $path);
		$path = preg_replace('/^' . preg_quote(DIRECTORY_SEPARATOR, '/') . '/', '', $path);
		$name = new BSArray;
		$name[] = get_class($this);
		$name->merge(explode('/', $path));
		$name = $name->join('.');

		$expire = $this->getUpdateDate();
		if (!$contents = BSController::getInstance()->getAttribute($name, $expire)) {
			BSController::includeFile('jsmin.php');
			$contents = JSMin::minify($this->getContents());
			BSController::getInstance()->setAttribute($name, $contents);
		}
		return $contents;
	}

	/**
	 * 基本情報を文字列で返す
	 *
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('JavaScriptファイル "%s"', $this->getPath());
	}
}

/* vim:set tabstop=4 ai: */
?>