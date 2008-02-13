<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage useragent
 */

/**
 * モバイルユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 * @abstract
 */
abstract class BSMobileUserAgent extends BSUserAgent {

	/**
	 * ドメインサフィックスを返す
	 *
	 * @access public
	 * @return string ドメインサフィックス
	 * @abstract
	 */
	abstract public function getDomainSuffix ();

	/**
	 * 全ての基本属性を返す
	 *
	 * @access public
	 * @return mixed[] 属性の配列
	 */
	public function getAttributes () {
		$controller = BSController::getInstance();
		$params = session_name() . '=' . session_id();
		if ($controller->isDebugMode()) {
			$params .= '&ua=' . $controller->getUserAgent()->getName();
		}
		$attributes = parent::getAttributes();
		$attributes['optional_params'] = $params;
		return $attributes;
	}

	/**
	 * ケータイ環境か？
	 *
	 * @access public
	 * @return boolean ケータイ環境ならTrue
	 */
	public function isMobile () {
		return true;
	}

	/**
	 * 全キャリアのドメインサフィックスを返す
	 *
	 * @access public
	 * @return string[] ドメインサフィックスの配列
	 * @static
	 */
	public static function getDomainSuffixes () {
		$patterns = array();
		foreach (array('Docomo', 'Au', 'SoftBank') as $carrier) {
			$name = sprintf('BS%sUserAgent', $carrier);
			$useragent = new $name;
			$patterns[$useragent->getType()] = $useragent->getDomainSuffix();
		}
		return $patterns;
	}
}

/* vim:set tabstop=4 ai: */
?>