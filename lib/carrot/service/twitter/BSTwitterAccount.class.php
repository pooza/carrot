<?php
/**
 * @package org.carrot-framework
 * @subpackage service.twitter
 */

/**
 * Twitterアカウント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSTwitterAccount {
	private $source;
	private $attributes = array();
	private $url;
	private $icon;

	/**
	 * @access public
	 * @param BSXMLElement $source status要素
	 */
	public function __construct (BSXMLElement $source = null) {
		$this->source = $source;
	}

	/**
	 * アカウントIDを返す
	 *
	 * @access public
	 * @return integer アカウントID
	 */
	public function getID () {
		return $this->getAttribute('id');
	}

	/**
	 * アカウントIDを設定
	 *
	 * @access public
	 * @param integer $id アカウントID
	 */
	public function setID ($id) {
		if ($this->getID() != $id) {
			$this->attributes = array('id' => $id);
			$this->source = null;
			$this->url = null;
			$this->icon = null;
		}
	}

	/**
	 * URLを返す
	 *
	 * @access public
	 * @return BSURL URL
	 */
	public function getURL () {
		if (!$this->url && $this->getAttribute('url')) {
			$this->url = new BSURL($this->getAttribute('url'));
		}
		return $this->url;
	}

	/**
	 * アイコン画像を返す
	 *
	 * @access public
	 * @return BSImage アイコン画像
	 */
	public function getIcon () {
		if (!$this->icon && $this->getAttribute('profile_image_url')) {
			try {
				$url = new BSURL($this->getAttribute('profile_image_url'));
				$this->icon = new BSImage;
				$this->icon->setImage($url->fetch());
			} catch (BSException $e) {
				return null;
			}
		}
		return $this->icon;
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return string 属性値
	 */
	public function getAttribute ($name) {
		if (!isset($this->attributes[$name]) && $this->source) {
			if ($element = $this->source->getElement($name)) {
				$this->attributes[$name] = $element->getBody();
			}
		}
		return $this->attributes[$name];
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Twitterアカウント "%d"', $this->getID());
	}
}

/* vim:set tabstop=4: */
