<?php
/**
 * @package org.carrot-framework
 * @subpackage media.music
 */

/**
 * 楽曲ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSMusicFile extends BSMediaFile {

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analyze () {
		parent::analyze();
		$this->attributes['width'] = BS_MUSIC_MP3_PLAYER_WIDTH;
		$this->attributes['height'] = BS_MUSIC_MP3_PLAYER_HEIGHT;
		$this->attributes['height_full'] = $this->attributes['height'];
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
			foreach (array('wma') as $musictype) {
				if (BSString::isContain('Audio: ' . $musictype, $this->output)) {
					return BSMIMEType::getType($musictype);
				}
			}
		}
		return $type;
	}

	/**
	 * プレイヤーの高さを返す
	 *
	 * @access public
	 * @return integer プレイヤーの高さ
	 */
	public function getPlayerHeight () {
		return BS_MUSIC_MP3_PLAYER_HEIGHT;
	}

	/**
	 * mp3に変換して返す
	 *
	 * @access public
	 * @return BSMusicFile 変換後ファイル
	 */
	public function convert () {
		$convertor = new BSMP3MediaConvertor;
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
		$container = new BSDivisionElement;
		$container->setAttribute('width', $this['width']);
		$container->setAttribute('height', $this['height']);
		if ($useragent->isHTML5Supported()) {
			$container->addElement($this->getAudioElement($params));
		} else {
			$container->addElement($this->getObjectElement($params));
		}
		return $container;
	}

	/**
	 * script要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSScriptElement 要素
	 */
	public function getScriptElement (BSParameterHolder $params) {
		throw new BSMediaException($this . 'はgetScriptElementに対応していません。');
	}

	/**
	 * object要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSObjectElement 要素
	 */
	public function getObjectElement (BSParameterHolder $params) {
		$element = new BSFlashObjectElement;
		$element->setURL(BSURL::getInstance()->setAttribute('path', BS_MUSIC_MP3_PLAYER_HREF));
		$element->setAttribute('width', $this['width']);
		$element->setAttribute('height', $this['height']);
		$element->setFlashVar('mp3', $this->getMediaURL($params)->getContents());
		$element->setFlashVar('autoload', 1);
		$element->setFlashVar('showstop', 1);
		$element->setFlashVar('showvolume', 1);
		$element->setFlashVar('showloading', 'autohide');
		return $element;
	}

	/**
	 * audio要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSObjectElement 要素
	 */
	public function getAudioElement (BSParameterHolder $params) {
		$element = new BSAudioElement;
		$element->registerSource($this->getMediaURL($params));
		return $element;
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
		return ($header['main_type'] == 'audio');
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('楽曲ファイル "%s"', $this->getShortPath());
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
	static public function search ($file, $class = 'BSMusicFile') {
		return parent::search($file, $class);
	}
}

/* vim:set tabstop=4: */
