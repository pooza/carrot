<?php
/**
 * @package org.carrot-framework
 * @subpackage media.movie
 */

/**
 * 動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
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
			$this->attributes['width'] = $matches[1];
			$this->attributes['height'] = $matches[2];
			$this->attributes['height_full'] = $matches[2] + $this->getPlayerHeight();
			$this->attributes['pixel_size'] = $matches[1] . '×' . $matches[2];
		}
	}

	/**
	 * ファイルの内容から、メディアタイプを返す
	 *
	 * fileinfoだけでは、wmvを認識できないことがある。
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function analyzeType () {
		if (($type = parent::analyzeType()) == BSMIMEType::DEFAULT_TYPE) {
			foreach (array('wmv', 'mpeg') as $type) {
				if (BSString::isContain('Video: ' . $type, $this->output)) {
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
		return BS_MOVIE_FLV_PLAYER_HEIGHT;
	}

	/**
	 * FLVに変換して返す
	 *
	 * @access public
	 * @return BSMovieFile 変換後ファイル
	 */
	public function convert () {
		$file = BSFileUtility::getTemporaryFile('flv', 'BSMovieFile');
		if ($this->getType() == BSMIMEType::getType('flv')) {
			$duplicated = $this->copyTo($file->getDirectory());
			$duplicated->rename($file->getName());
			$file = $duplicated;
		} else {
			$command = self::getCommandLine();
			$command->addValue('-y');
			$command->addValue('-i');
			$command->addValue($this->getPath());
			$command->addValue('-vcodec');
			$command->addValue(BS_MOVIE_VIDEO_CODEC);
			$command->addValue('-acodec');
			$command->addValue(BS_MOVIE_AUDIO_CODEC);
			$command->addValue($file->getPath());
			$command->addValue('2>&1', null);
			$this->output = $command->getResult()->join("\n");
			BSLogManager::getInstance()->put($this . 'をflvに変換しました。', $this);
		}
		return new self($file->getPath());
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getElement (BSParameterHolder $params) {
		$element = parent::getElement($params);
		if ($inner = $element->getElement('div')) { //Gecko対応
			$inner->setStyles($this->getStyles($params));
		}
		return $element;
	}

	/**
	 * script要素を返す
	 *
	 * @access protected
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
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
	 * @return BSXMLElement 要素
	 */
	protected function getObjectElement (BSParameterHolder $params) {
		$element = new BSFlashObjectElement;
		$element->setURL(BSURL::getInstance()->setAttribute('path', BS_MOVIE_FLV_PLAYER_HREF));
		$element->setFlashVar('config', $this->getPlayerConfig($params));
		return $element;
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
					'height' => BS_MOVIE_FLV_PLAYER_HEIGHT,
					//'fullscreen' => false,
				),
			),
		);
		return BSJavaScriptUtility::quote($config);
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
			case BSMIMEType::getType('3gp'):
				return parent::search($file, 'BS3GPMovieFile');
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
