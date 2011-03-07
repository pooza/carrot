<?php
/**
 * @package org.carrot-framework
 * @subpackage media.flash
 */

/**
 * Flashムービーファイル
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSFlashFile extends BSMediaFile {

	/**
	 * ファイルを解析
	 *
	 * @access protected
	 */
	protected function analyze () {
		$info = getimagesize($this->getPath());
		if (!$info || ($info['mime'] != BSMIMEType::getType('swf'))) {
			throw new BSMediaException($this . 'はFlashムービーではありません。');
		}
		$this->attributes['path'] = $this->getPath();
		$this->attributes['type'] = $info['mime'];
		$this->attributes['width'] = $info[0];
		$this->attributes['height'] = $info[1];
		$this->attributes['height_full'] = $info[1];
		$this->attributes['pixel_size'] = $this['width'] . '×' . $this['height'];
		$this->attributes['aspect'] = $this['width'] / $this['height'];
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
		if (!$useragent) {
			$useragent = BSRequest::getInstance()->getUserAgent();
		}
		if ($useragent->isMobile()) {
			$params['url'] = $this->getMediaURL($params);
			return $useragent->getFlashElement($params);
		}
		$container = parent::getElement($params);
		if (($info = $params['thumbnail']) && ($inner = $container->getElement('div'))) {
			$info = new BSArray($info);
			$image = new BSImageElement;
			$image->setAttributes($info);
			$image->registerStyleClass('deny_take_out');
			$inner->addElement($image);
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
		$element = new BSScriptElement;
		$body = new BSStringFormat('swfobject.embedSWF(%s,%s,%d,%d,%s,%s,%s,%s);');
		$body[] = BSJavaScriptUtility::quote($this->getMediaURL($params)->getContents());
		$body[] = BSJavaScriptUtility::quote($params['container_id']);
		$body[] = $this['width'];
		$body[] = $this['height'];
		$body[] = BSJavaScriptUtility::quote(BS_FLASH_PLAYER_VER);
		$body[] = BSJavaScriptUtility::quote(BS_FLASH_INSTALLER_HREF);
		$body[] = BSJavaScriptUtility::quote(null);
		$body[] = BSJavaScriptUtility::quote(array('wmode' => 'transparent'));
		$element->setBody($body->getContents());
		return $element;
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
		$element->setURL($this->getMediaURL($params));
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
		return ($this->analyzeType() == BSMIMEType::getType('swf'));
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('Flashムービーファイル "%s"', $this->getShortPath());
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
	static public function search ($file, $class = 'BSFlashFile') {
		return parent::search($file, $class);
	}
}

/* vim:set tabstop=4: */
