<?php
/**
 * @package org.carrot-framework
 * @subpackage net.mail
 */

/**
 * メールURL
 *
 * mailtoとかtelとか。
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSMailToURL extends BSURL {

	/**
	 * 内容を返す
	 *
	 * @access public
	 * @return string URL
	 */
	public function getContents () {
		if (!$this->contents) {
			$this->contents = $this['schema'] . ':' . $this['path'];
		}
		return $this->contents;
	}

	/**
	 * 属性を設定
	 *
	 * @access public
	 * @param string $name 属性の名前
	 * @param mixed $value 値
	 * @return BSURL 自分自身
	 */
	public function setAttribute ($name, $value) {
		switch ($name) {
			case 'scheme':
			case 'path':
				$this->attributes[$name] = $value;
				break;
			default:
				throw new BSNetException('"%s"は正しくない属性名です。', $name);
		}
		$this->contents = null;
		return $this;
	}
}

/* vim:set tabstop=4: */
