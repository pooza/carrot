<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * 3GPP動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BS3GPPMovieFile extends BSQuickTimeMovieFile {

	/**
	 * 表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function createElement (BSParameterHolder $params, BSUserAgent $useragent = null) {
		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}
		if ($useragent->isMobile()) {
			$params = BSArray::encode($params);
			$params['url'] = $this->createURL($params)->getContents();
			if (BSString::isBlank($params['type'])) {
				$params['type'] = $this->getType();
			}
			if (BSString::isBlank($params['label'])) {
				$params['label'] = $this->getBaseName();
			}
			return $useragent->createMovieElement($params);
		}
		return parent::createElement($params, $useragent);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('3GPP動画ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
