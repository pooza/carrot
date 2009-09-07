<?php
/**
 * @package org.carrot-framework
 * @subpackage xml.xhtml
 */

/**
 * form要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSFormElement extends BSXMLElement {
	private $useragent;
	const ATTACHABLE_TYPE = 'multipart/form-data';

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = 'form', BSUserAgent $useragent = null) {
		parent::__construct($name);
		$this->setRawMode(true);

		if ($useragent) {
			$this->useragent = $useragent;
		} else {
			$this->useragent = BSRequest::getInstance()->getUserAgent();
		}
		if ($this->useragent->isMobile()) {
			foreach ($this->useragent->getAttribute('query') as $key => $value) {
				$this->addHiddenField($key, $value);
			}
		}
	}

	/**
	 * メソッドを返す
	 *
	 * @access public
	 * @return string method属性
	 */
	public function getMethod () {
		return $this->getAttribute('method');
	}

	/**
	 * メソッドを設定
	 *
	 * @access public
	 * @param string $method メソッド
	 */
	public function setMethod ($method) {
		$this->setAttribute('method', BSString::toUpper($method));
		if ($this->getMethod() == 'POST') {
			$this->addHiddenField('dummy', '符号形式識別用文字列');
			$this->addHiddenField('submit', 1);
		}
	}

	/**
	 * フォームアクションを返す
	 *
	 * @access public
	 * @return string action属性
	 */
	public function getAction () {
		return $this->getAttribute('action');
	}

	/**
	 * フォームアクションを設定
	 *
	 * @access public
	 * @param mixed $action 文字列、URL、パラメータ配列等
	 */
	public function setAction ($action) {
		if ($action instanceof BSHTTPRedirector) {
			$this->setAttribute('action', $action->getURL()->getContents());
		} else if ($action instanceof BSParameterHolder) {
			if (BSString::isBlank($action['path'])) {
				$url = BSURL::getInstance(null, 'BSCarrotURL');
				if (BSString::isBlank($action['module'])) {
					if (BSString::isBlank($action['action'])) {
						$url['action'] = BSController::getInstance()->getAction();
					} else {
						$url['module'] = BSController::getInstance()->getModule();
						$url['action'] = $action['action'];
					}
				} else {
					$url['module'] = $action['module'];
					if (BSString::isBlank($action['action'])) {
						$url['action'] = 'Default';
					} else {
						$url['action'] = $action['action'];
					}
				}
				return $this->setAction($url);
			}
			$this->setAction($action['path']);
		} else {
			$this->setAttribute('action', $action);
		}
	}

	/**
	 * ファイル添付が可能か？
	 *
	 * @access public
	 * @return boolean 可能ならTrue
	 */
	public function isAttachable () {
		return $this->getAttribute('enctype') == self::ATTACHABLE_TYPE;
	}

	/**
	 * ファイル添付が可能かを設定
	 *
	 * @access public
	 * @param boolean $flag ファイル添付が可能ならTrue
	 */
	public function setAttachable ($flag) {
		if ($flag) {
			$this->setAttribute('enctype', self::ATTACHABLE_TYPE);
		} else {
			$this->removeAttribute('enctype');
		}
	}

	/**
	 * hidden値を加える
	 *
	 * @access public
	 * @param string $name 名前
	 * @param string $value 値
	 * @return BSXMLElement 追加されたinput要素
	 */
	public function addHiddenField ($name, $value) {
		$hidden = $this->createElement('input');
		$hidden->setAttribute('type', 'hidden');
		$hidden->setAttribute('name', $name);
		$hidden->setAttribute('value', $value);
		return $hidden;
	}
}

/* vim:set tabstop=4: */