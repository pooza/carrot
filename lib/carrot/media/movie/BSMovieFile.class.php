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
	protected function analyze () {
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
		if (mb_ereg(' ([[:digit:]]{,4})x([[:digit:]]{,4})', $this->output, $matches)) {
			$this->attributes['width'] = $matches[1];
			$this->attributes['height'] = $matches[2];
			$this->attributes['height_full'] = $matches[2] + BS_MOVIE_PLAYER_HEIGHT;
			$this->attributes['pixel_size'] = $matches[1] . '×' . $matches[2];
		}
		$this->attributes['type'] = $this->analyzeType($this->output);
	}

	private function analyzeType ($output) {
		$patterns = new BSArray(array(
			'Input #[[:digit:]]+, ([[:alnum:]]+)',
			'Video: ([[:alnum:]]+)',
		));
		foreach ($patterns as $pattern) {
			if (mb_ereg($pattern, $output, $matches)) {
				if (!BSString::isBlank($type = BSMovieUtility::getType($matches[1]))) {
					return $type;
				}
			}
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
	public function getImageElement (BSParameterHolder $params) {
		$element = parent::getImageElement($params);
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
		$body[] = BSJavaScriptUtility::quote(BS_MOVIE_PLAYER_HREF);
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
		$element = BSFlashUtility::getObjectElement(
			BSURL::getInstance()->setAttribute('path', BS_MOVIE_PLAYER_HREF)
		);
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
					'height' => BS_MOVIE_PLAYER_HEIGHT,
					'fullscreen' => false,
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
}

/* vim:set tabstop=4: */
