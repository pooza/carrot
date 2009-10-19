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
	private $output;

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analize () {
		$command = BSMovieUtility::getCommandLine();
		$command->addValue('-i');
		$command->addValue($this->getPath());
		$command->addValue('2>&1', null);
		$this->output = $command->getResult()->join("\n");

		if (mb_ereg('frame rate: [^\\-]+ -> ([.[:digit:]]+)', $this->output, $matches)) {
			$this->attributes['frame_rate'] = (float)$matches[1];
		}
		if (mb_ereg('Duration: ([.:[:digit:]]+),', $this->output, $matches)) {
			$this->attributes['duration'] = $matches[1];
			$sec = BSString::explode(':', $matches[1]);
			$this->attributes['seconds'] = ($sec[0] * 3600) + ($sec[1] * 60) + $sec[2];
		}
		if (mb_ereg(' ([[:digit:]]{,4}x[[:digit:]]{,4}),', $this->output, $matches)) {
			$size = BSString::explode('x', $matches[1]);
			$this->attributes['width'] = $size[0];
			$this->attributes['height'] = $size[1];
			$this->attributes['height_full'] = $size[1] + BS_MOVIE_PLAYER_HEIGHT;
			$this->attributes['pixel_size'] = $size[0] . '×' . $size[1];
		}
		if (mb_ereg(' Video: ([[:alnum:]]+)', $this->output, $matches)) {
			$this->attributes['type'] = BSMovieUtility::getType($matches[1]);
		}
	}

	/**
	 * FLVに変換して返す
	 *
	 * @access public
	 * @return BSMovieFile 変換後ファイル
	 */
	public function convert () {
		$file = BSFileUtility::getTemporaryFile('.flv', 'BSMovieFile');
		if ($this->getType() == BSMIMEType::getType('.flv')) {
			$duplicated = $this->copyTo($file->getDirectory());
			$duplicated->rename($file->getName());
			$file = $duplicated;
		} else {
			$command = BSMovieUtility::getCommandLine();
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
			BSController::getInstance()->putLog($this . 'をflvに変換しました。', $this);
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
	public function getImageElement (BSParameterHolder $params) {
		$element = parent::getImageElement($params);

		$style = $this->getPixelSizeCSSSelector($params);
		$element->setAttribute('style', $style->getContents());
		if ($inner = $element->getElement('div')) { //Gecko対応
			$inner->setAttribute('style', $style->getContents());
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
		$element = BSJavaScriptUtility::getScriptElement();
		$body = new BSStringFormat('flowplayer(%s, %s, %s);');
		$body[] = BSJavaScriptUtility::quote($params['container_id']);
		$body[] = BSJavaScriptUtility::quote(BS_MOVIE_PLAYER_HREF);
		$body[] = BSJavaScriptUtility::quote($this->getMediaURL($params)->getContents());
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
		$element = BSFlashUtility::getObjectElement(
			BSURL::getInstance()->setAttribute('path', BS_MOVIE_PLAYER_HREF)
		);
		$config = array(
			'clip' => array('url' => $this->getMediaURL($params)->getContents()),
		);
		$param = $element->createElement('param');
		$param->setAttribute('name', 'flashvars');
		$param->setAttribute('value', 'config=' . BSJavaScriptUtility::quote($config));
		return $element;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('動画ファイル "%s"', $this->getShortPath());
	}
}

/* vim:set tabstop=4: */
