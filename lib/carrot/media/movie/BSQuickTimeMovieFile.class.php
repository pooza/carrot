<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * QuickTime動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSQuickTimeMovieFile extends BSMovieFile {

	/**
	 * 表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function getElement (BSParameterHolder $params, BSUserAgent $useragent = null) {
		$this->resizeByWidth($params, $useragent);
		if ($params['mode'] == 'shadowbox') {
			return $this->getShadowboxElement($params);
		}

		$container = new BSDivisionElement;
		$container->registerStyleClass($params['style_class']);
		$container->setStyles($this->getStyles($params));
		$object = $container->addElement(new BSQuickTimeObjectElement);
		$object->setAttribute('type', $this->getType());
		$object->setAttribute('width', $params['width']);
		$object->setAttribute('height', $params['height']);
		$object->setURL($this->getMediaURL($params));
		foreach (array('kioskmode') as $key) {
			if ($params->hasParameter($key)) {
				$object->setParameter($key, $params[$key]);
			}
		}
		return $container;
	}

	/**
	 * プレイヤーの高さを返す
	 *
	 * @access public
	 * @return integer プレイヤーの高さ
	 */
	public function getPlayerHeight () {
		return BS_MOVIE_QUICKTIME_PLAYER_HEIGHT;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('QuickTime動画ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
