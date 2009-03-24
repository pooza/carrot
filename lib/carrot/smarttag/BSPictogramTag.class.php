<?php
/**
 * @package org.carrot-framework
 * @subpackage smarttag
 */

BSUtility::includeFile('mpc/MobilePictogramConverter.php');

/**
 * 絵文字タグ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSPictogramTag extends BSSmartTag {
	static private $agents;

	/**
	 * タグ名を返す
	 *
	 * @access public
	 * @return string タグ名
	 */
	public function getTagName () {
		return 'pictogram';
	}

	/**
	 * 置換して返す
	 *
	 * @access public
	 * @param string $body 置換対象文字列
	 * @return string 置換された文字列
	 * @abstract
	 */
	public function execute ($body) {
		$useragent = self::getUserAgent($this->tag[2]);
		$raw = $useragent->getPictogram($this->tag[1]);
		if ($this->isRawMode()) {
			$replace = $raw;
		} else {
			$useragent->getMPC()->setString($raw);
			$replace = $useragent->getMPC()->convert($this->tag[2], MPC_TO_OPTION_IMG);
		}
		return str_replace($this->contents, $replace, $body);
	}

	private function isRawMode () {
		$request = BSRequest::getInstance()->getUserAgent();
		$current = self::getUserAgent($this->tag[2]);
		return $request->isMobile() && ($current->getType() == $request->getType());
	}

	static private function getUserAgent ($code) {
		if (!self::$agents) {
			self::$agents = new BSArray;
		}
		if (!self::$agents[$code]) {
			$classes = array(
				MPC_FROM_EZWEB => 'Au',
				MPC_FROM_FOMA => 'Docomo',
				MPC_FROM_SOFTBANK => 'SoftBank',
			);
			self::$agents[$code] = BSClassLoader::getInstance()->getObject(
				$classes[$code],
				'UserAgent'
			);
		}
		return self::$agents[$code];
	}
}

/* vim:set tabstop=4: */
