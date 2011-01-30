<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * 動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMovieFile extends BSMediaFile {

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analyze () {
		parent::analyze();
		if (mb_ereg('frame rate: [^\\-]+ -> ([.[:digit:]]+)', $this->output, $matches)) {
			$this->attributes['frame_rate'] = (float)$matches[1];
		}
		if (mb_ereg(' ([[:digit:]]{2,4})x([[:digit:]]{2,4})', $this->output, $matches)) {
			$this->attributes['width'] = (int)$matches[1];
			$this->attributes['height'] = (int)$matches[2];
			$this->attributes['height_full'] = $matches[2] + $this->getPlayerHeight();
			$this->attributes['pixel_size'] = $this['width'] . '×' . $this['height'];
			$this->attributes['aspect'] = $this['width'] / $this['height'];
		}
	}

	/**
	 * ファイルの内容から、メディアタイプを返す
	 *
	 * fileinfoだけでは認識できないメディアタイプがある。
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function analyzeType () {
		if (($type = parent::analyzeType()) == BSMIMEType::DEFAULT_TYPE) {
			if (!$this->attributes->count()) {
				$this->analyze();
			}
			if ($this->getSuffix() == '.3g2') {
				return BSMIMEType::getType('3g2');
			}
			foreach (array('wmv', 'mpeg') as $movietype) {
				if (BSString::isContain('Video: ' . $movietype, $this->output)) {
					return BSMIMEType::getType($movietype);
				}
			}
		}
		return $type;
	}

	/**
	 * 動画トラックを持つか？
	 *
	 * @access public
	 * @return boolean 動画トラックを持つならTrue
	 */
	public function hasMovieTrack () {
		if (!$this->attributes->count()) {
			$this->analyze();
		}
		return ($this['width'] && $this['height']);
	}

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
	 * @return BSMovieFile 変換後ファイル
	 */
	public function convert () {
		$convertor = new BSFLVMediaConvertor;
		return $convertor->execute($this);
	}

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

		$container = parent::getElement($params);
		if ($inner = $container->getElement('div')) { //Gecko対応
			$inner->setStyles($this->getStyles($params));
		}
		return $container;
	}

	/**
	 * script要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSScriptElement 要素
	 */
	protected function getScriptElement (BSParameterHolder $params) {
		$element = new BSScriptElement;
		$body = new BSStringFormat('flowplayer(%s, %s, %s);');
		$body[] = BSJavaScriptUtility::quote($params['container_id']);
		$body[] = BSJavaScriptUtility::quote(array(
			'src' => BS_MOVIE_FLV_PLAYER_HREF,
			'wmode' => 'transparent',
		));
		$body[] = $this->getPlayerConfig($params);
		$element->setBody($body->getContents());
		return $element;
	}

	/**
	 * object要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSObjectElement 要素
	 */
	protected function getObjectElement (BSParameterHolder $params) {
		$element = new BSFlashObjectElement;
		$element->setURL(BSURL::getInstance()->setAttribute('path', BS_MOVIE_FLV_PLAYER_HREF));
		$element->setFlashVar('config', $this->getPlayerConfig($params));
		return $element;
	}

	/**
	 * Shadowboxへのリンク要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSShadowboxAnchorElement 要素
	 */
	protected function getShadowboxElement (BSParameterHolder $params) {
		$params = new BSArray($params);
		if (!$params['width_movie']) {
			$params['width_movie'] = $params['width'];
		}
		if (!$params['height_movie']) {
			$params['height_movie'] = $params['height'];
		}

		$container = new BSShadowboxAnchorElement;
		$container->setWidth($params['width_movie']);
		$container->setHeight($params['height_movie']);
		$container->setURL($this->getMediaURL($params));
		if ($info = $params['thumbnail']) {
			$info = new BSArray($info);
			$image = new BSImageElement;
			$image->setAttributes($info);
			$image->registerStyleClass('deny_take_out');
			$container->addElement($image);
		} else {
			$container->setBody($params['label']);
		}
		return $container;
	}

	/**
	 * flowplayerの設定を返す
	 *
	 * @access private
	 * @param BSParameterHolder $params パラメータ配列
	 * @return string JSONシリアライズされた設定
	 */
	private function getPlayerConfig (BSParameterHolder $params) {
		$config = array(
			'clip' => array(
				'scaling' => 'fit',
				'autoPlay' => false,
				'autoBuffering' => true,
				'url' => $this->getMediaURL($params)->getContents(),
			),
			'plugins' => array(
				'controls' => array(
					'url' => BS_MOVIE_FLV_PLAYER_CONTROL_BAR_HREF,
					'opacity' => 0.9,
					'fullscreen' => ($params['mode'] != 'noscript'),
				),
			),
		);
		return BSJavaScriptUtility::quote($config);
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!parent::validate()) {
			return false;
		}
		$header = new BSContentTypeMIMEHeader;
		$header->setContents($this->analyzeType());
		return ($header['main_type'] == 'video');
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('動画ファイル "%s"', $this->getShortPath());
	}

	/**
	 * 探す
	 *
	 * @access public
	 * @param mixed $file パラメータ配列、BSFile、ファイルパス文字列
	 * @param string $class クラス名
	 * @return BSFile ファイル
	 * @static
	 */
	static public function search ($file, $class = 'BSMovieFile') {
		if (!$file = parent::search($file, $class)) {
			return;
		}
		switch ($file->getType()) {
			case BSMIMEType::getType('3g2'):
				return parent::search($file, 'BS3GPP2MovieFile');
			case BSMIMEType::getType('3gp'):
				return parent::search($file, 'BS3GPPMovieFile');
			case BSMIMEType::getType('mov'):
				return parent::search($file, 'BSQuickTimeMovieFile');
			case BSMIMEType::getType('mpeg'):
				return parent::search($file, 'BSMPEG1MovieFile');
			case BSMIMEType::getType('mp4'):
				return parent::search($file, 'BSMPEG4MovieFile');
			case BSMIMEType::getType('wmv'):
				return parent::search($file, 'BSWindowsMediaMovieFile');
		}
		return $file;
	}
}

/* vim:set tabstop=4: */
