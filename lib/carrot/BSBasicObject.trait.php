<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * シングルトン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
trait BSBasicObject {

	/**
	 * @access public
	 * @param string $name プロパティ名
	 * @return mixed 各種オブジェクト
	 */
	public function __get ($name) {
		switch ($name) {
			case 'controller':
			case 'request':
			case 'user':
			case 'loader':
			case 'database':
				return BSUtility::executeMethod($name, 'getInstance');
			case 'useragent':
				return BSRequest::getInstance()->getUserAgent();
			case 'translator':
				return BSTranslateManager::getInstance();
		}
	}
}

/* vim:set tabstop=4: */
