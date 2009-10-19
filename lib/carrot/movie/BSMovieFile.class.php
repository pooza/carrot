<?php
/**
 * @package org.carrot-framework
 * @subpackage movie
 */

/**
 * 動画ファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMovieFile extends BSFile implements ArrayAccess {
	private $attributes;
	private $output;

	/**
	 * 属性を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 全ての属性を返す
	 *
	 * @access public
	 * @return BSArray 全ての属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray;
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
		return $this->attributes;
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->getAttributes()->getParameter('type');
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
	 * 要素が存在するか？
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return boolean 要素が存在すればTrue
	 */
	public function offsetExists ($key) {
		return $this->getAttributes()->hasParameter($key);
	}

	/**
	 * 要素を返す
	 *
	 * @access public
	 * @param string $key 添え字
	 * @return mixed 要素
	 */
	public function offsetGet ($key) {
		return $this->getAttribute($key);
	}

	/**
	 * 要素を設定
	 *
	 * @access public
	 * @param string $key 添え字
	 * @param mixed 要素
	 */
	public function offsetSet ($key, $value) {
		throw new BSMovieException($this . 'の属性を設定できません。');
	}

	/**
	 * 要素を削除
	 *
	 * @access public
	 * @param string $key 添え字
	 */
	public function offsetUnset ($key) {
		throw new BSMovieException($this . 'の属性を削除できません。');
	}

	/**
	 * ムービー表示用のXHTML要素を返す
	 *
	 * @access public
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	public function getImageElement (BSParameterHolder $params) {
		$style = $this->getPixelSizeCSSSelector($params);

		$root = new BSXMLElement('div');
		$root->setAttribute('style', $style->getContents()); //Gecko対応
		if (!BSString::isBlank($params['style_class'])) {
			$root->setAttribute('class', $params['style_class']);
		}

		$container = $root->createElement('div');
		$container->setAttribute('id', $this->getContainerID());
		$container->getBody('Loading...');
		$container->setAttribute('style', $style->getContents());

		$root->addElement($this->getScriptElement($params));
		return $root;
	}

	/**
	 * スタイル属性を返す
	 *
	 * @access private
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSCSSSelector スタイル属性
	 */
	private function getPixelSizeCSSSelector (BSParameterHolder $params) {
		$style = new BSCSSSelector;
		if ($params['width']) {
			$style['width'] = $params['width'] . 'px';
		} else {
			$style['width'] = $this['width'] . 'px';
		}
		if ($params['height']) {
			$style['height'] = $params['height'] . 'px';
		} else {
			$style['height'] = $this['height_full'] . 'px';
		}
		return $style;
	}

	/**
	 * div要素のIDを返す
	 *
	 * @access private
	 * @return string div要素のID
	 */
	private function getContainerID () {
		return 'flv_' . $this->getID();
	}

	/**
	 * script要素を返す
	 *
	 * @access private
	 * @param BSParameterHolder $params パラメータ配列
	 * @return BSXMLElement 要素
	 */
	private function getScriptElement (BSParameterHolder $params) {
		$element = BSJavaScriptUtility::getScriptElement();
		$body = new BSStringFormat('flowplayer(%s, %s, %s);');
		$body[] = BSJavaScriptUtility::quote($this->getContainerID());
		$body[] = BSJavaScriptUtility::quote(BS_MOVIE_PLAYER_HREF);
		$body[] = BSJavaScriptUtility::quote(
			$params['href_prefix'] . $this->getName() . $params['href_suffix']
		);
		$element->setBody($body->getContents());
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
