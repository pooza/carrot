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
		$name = new BSArray;
		$name[] = get_class($this);
		$path = str_replace(
			BSController::getInstance()->getPath('js') . DIRECTORY_SEPARATOR,
			'',
			$this->getPath()
		);
		$name->merge(explode(DIRECTORY_SEPARATOR, $path));
		$name = $name->join(',');

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