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
	 * div要素のIDを生成して返す
	 *
	 * @access protected
	 * @return string div要素のID
	 */
	protected function createContainerID () {
		return BSCrypt::digest(array(
			get_class($this),
			$this->getID(),
			BSUtility::getUniqueID(),
		));
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
		$params = BSArray::create($params);
		$this->resizeByWidth($params, $useragent);

		if ($useragent->isMobile()) {
			$params['url'] = $this->createURL($params);
			$container = $useragent->createFlashElement($params);
		} else {
			$container = new BSDivisionElement;
			$container->registerStyleClass($params['style_class']);
			if ($params['mode'] == 'noscript') {
				$container->setStyles($this->getStyles($params));
				$container->addElement($this->createObjectElement($params));
			} else {
				if (BSString::isBlank($params['container_id'])) {
					$params['container_id'] = $this->createContainerID();
					$inner = $container->addElement(new BSDivisionElement);
					$inner->setID($params['container_id']);
				}
				$container->addElement($this->createScriptElement($params));
			}
			if (($info = $params['thumbnail']) && ($inner = $container->getElement('div'))) {
				$image = $inner->addElement(new BSImageElement);
				$image->setAttributes(new BSArray($info));
			}
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
	public function createScriptElement (BSParameterHolder $params) {
		$serializer = new BSJSONSerializer;
		$element = new BSScriptElement;
		$statement = new BSStringFormat('swfobject.embedSWF(%s,%s,%d,%d,%s,%s,%s,%s);');
		$statement[] = $serializer->encode($this->createURL($params)->getContents());
		$statement[] = $serializer->encode($params['container_id']);
		$statement[] = $this['width'];
		$statement[] = $this['height'];
		$statement[] = $serializer->encode(BS_FLASH_PLAYER_VER);
		$statement[] = $serializer->encode(BS_FLASH_INSTALLER_HREF);
		$statement[] = $serializer->encode(null);
		$statement[] = $serializer->encode(array('wmode' => 'transparent'));
		$element->setBody($statement);
		return $element;
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
		$element->setURL($this->createURL($params));
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
