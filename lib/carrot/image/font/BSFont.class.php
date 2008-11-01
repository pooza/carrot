<?php
/**
 * @package org.carrot-framework
 * @subpackage image.font
 */

/**
 * フォント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id: BSColor.class.php 573 2008-09-13 07:38:10Z pooza $
 */
class BSFont extends BSParameterHolder {
	private $file;

	/**
	 * @access public
	 */
	public function __construct ($name, $params) {
		$params['names']['default'] = $name;
		$this->setParameters($params);
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @param string $language 言語
	 * @return string 名前
	 */
	public function getName ($language = 'default') {
		if (isset($this['names'][$language])) {
			return $this['names'][$language];
		} else {
			return $this['names']['default'];
		}
	}

	/**
	 * フォントファイルを返す
	 *
	 * @access public
	 * @return BSFile フォントファイル
	 */
	public function getFile () {
		if (!$this->file) {
			$this->file = $this->getManager()->getDirectory()->getEntry($this->getName());
		}
		return $this->file;
	}

	/**
	 * フォントマネージャを返す
	 *
	 * @access private
	 * @return BSFile フォントマネージャ
	 */
	private function getManager () {
		return BSFontManager::getInstance();
	}
}

/* vim:set tabstop=4 ai: */
?>