<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.movie
 */

/**
 * MPEG4動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFLVMovieFile extends BSMovieFile {

	/**
	 * プレイヤーの高さを返す
	 *
	 * @access public
	 * @return integer プレイヤーの高さ
	 */
	public function getPlayerHeight () {
		return BS_MOVIE_FLV_PLAYER_HEIGHT;
	}

	/**
	 * FLVに変換して返す
	 *
	 * @access public
	 * @param BSMediaConvertor $convertor コンバータ
	 * @return BSMovieFile 変換後ファイル
	 */
	public function convert (BSMediaConvertor $convertor = null) {
		if (!$convertor) {
			$convertor = new BSFLVMediaConvertor;
		}
		return $convertor->execute($this);
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('FLV動画ファイル "%s"', $this->getShortPath());
	}

	/**
	 * 表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @param BSUserAgent $useragent 対象ブラウザ
	 * @return BSDivisionElement 要素
	 */
	public function createElement (BSParameterHolder $params, BSUserAgent $useragent = null) {
		switch ($params['mode']) {
			case 'shadowbox':
				return $this->createShadowboxElement($params);
			case 'lity':
				return $this->createLityElement($params);
		}

		$params = BSArray::create($params);
		$this->resizeByWidth($params, $useragent);
		$container = new BSDivisionElement;
		$container->registerStyleClass($params['style_class']);
		$container->setStyles($this->getStyles($params));
		$container->setStyle('height', 'auto');
		if ($element = $this->createObjectElement($params)) {
			$container->addElement($element);
		}
		if ($inner = $container->getElement('div')) { //Gecko対応
			$inner->setStyles($this->getStyles($params));
		}
		return $container;
	}

	/**
	 * object要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSObjectElement 要素
	 */
	public function createObjectElement (BSParameterHolder $params) {
		$element = new BSFlashObjectElement;
		$element->setURL(BSURL::create(BS_MOVIE_FLV_PLAYER_HREF));
		$element->setAttribute('width', $params['width']);
		$element->setAttribute('height', $params['height'] + $this->getPlayerHeight());
		$element->setParameter('allowfullscreen', 'true');
		$element->setParameter('allowscriptaccess', 'always');

		$url = $this->createURL($params);
		$url['query'] = null; //クエリー文字列のあるURLを指定するとエラーになる。
		$element->setFlashVar('file', $url->getContents());
		return $element;
	}
}

