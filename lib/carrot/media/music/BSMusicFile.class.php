<?php
/**
 * @package org.carrot-framework
 * @subpackage media.music
 */

/**
 * 楽曲ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
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
		$file = BSFileUtility::getTemporaryFile('mp3', 'BSMusicFile');
		if ($this->getType() == BSMIMEType::getType('mp3')) {
			$duplicated = $this->copyTo($file->getDirectory());
			$duplicated->rename($file->getName());
			$file = $duplicated;
		} else {
			$command = self::getCommandLine();
			$command->addValue('-y');
			$command->addValue('-i');
			$command->addValue($this->getPath());
			$command->addValue($file->getPath());
			$command->addValue('2>&1', null);
			$this->output = $command->getResult()->join("\n");
			BSLogManager::getInstance()->put($this . 'をmp3に変換しました。', $this);
		}
		return new self($file->getPath());
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSDivisionElement 要素
	 */
	public function getElement (BSParameterHolder $params) {
		$container = new BSDivisionElement;
		$container->setAttribute('width', $this['width']);
		$container->setAttribute('height', $this['height']);
		$container->addElement($this->getObjectElement($params));
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
		throw new BSMediaException($this . 'はgetScriptElementに対応していません。');
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
