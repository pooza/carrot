<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage xml.xhtml.object
 */

/**
 * YouTube用object要素
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSYouTubeObjectElement extends BSObjectElement {

	/**
	 * @access public
	 * @param string $name 要素の名前
	 * @param BSUserAgent $useragent 対象UserAgent
	 */
	public function __construct ($name = null, BSUserAgent $useragent = null) {
		parent::__construct($name, $useragent);
		$this->setAttribute('type', BSMIMEType::getType('swf'));
		$this->setParameter('allowFullScreen', 'true');
	}

	/**
	 * ムービーを設定
	 *
	 * @access public
	 * @param integer $id ムービーID
	 * @param BSParameterHolder $params パラメータ配列
	 */
	public function setMovie ($id, BSParameterHolder $params = null) {
		$params = BSArray::create($params);
		$params->removeParameter('width');
		$params->removeParameter('height');

		$url = BSURL::create();
		$url['host'] = BSYouTubeService::DEFAULT_HOST;
		$url['path'] = '/v/' . $id;
		$url->setParameters($params);

		$this->setAttribute('data', $url->getContents());
		$this->setParameter('movie', $url->getContents());
	}
}

/* vim:set tabstop=4: */
