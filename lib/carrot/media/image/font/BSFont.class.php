<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.font
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
		$params['name']['default'] = $name;
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
		if (isset($this['name'][$language])) {
			return $this['name'][$language];
		} else {
			return $this['name']['default'];
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

	private function getManager () {
		return BSFontManager::getInstance();
	}
}

/* vim:set tabstop=4: */
