<?php
/**
 * @package jp.co.b-shock.carrot
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
			foreach (['wma'] as $type) {
				if (BSString::isContain('Audio: ' . $type, $this->output)) {
					return BSMIMEType::getType($type);
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
	 * @param BSMediaConvertor $convertor コンバータ
	 * @return BSMovieFile 変換後ファイル
	 */
	public function convert (BSMediaConvertor $convertor = null) {
		if (!$convertor) {
			$convertor = new BSMP3MediaConvertor;
		}
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
	public function createElement (BSParameterHolder $params, BSUserAgent $useragent = null) {
		if (!$useragent) {
			$useragent = $this->request->getUserAgent();
		}

		$params = BSArray::create($params);
		$this->resizeByWidth($params, $useragent);

		$container = new BSDivisionElement;
		$container->setAttribute('width', $this['width']);
		$container->setAttribute('height', $this['height']);
		if ($useragent->hasSupport('html5_audio')) {
			$container->addElement($this->createAudioElement($params));
		} else {
			$container->addElement($this->createObjectElement($params));
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
		$element->setURL(BSURL::create(BS_MUSIC_MP3_PLAYER_HREF));
		$element->setAttribute('width', BS_MUSIC_MP3_PLAYER_WIDTH);
		$element->setAttribute('height', BS_MUSIC_MP3_PLAYER_HEIGHT);
		$element->setParameter('allowscriptaccess', 'always');

		$url = $this->createURL($params);
		$url['query'] = null; //クエリー文字列のあるURLを指定するとエラーになる。
		$element->setFlashVar('file', $url->getContents());
		return $element;
	}

	/**
	 * audio要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSAudioElement 要素
	 */
	public function createAudioElement (BSParameterHolder $params) {
		$element = new BSAudioElement;
		$element->registerSource($this->createURL($params));
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
		return (BSMIMEUtility::getMainType($this->analyzeType()) == 'audio');
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
