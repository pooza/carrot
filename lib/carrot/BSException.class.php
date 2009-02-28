<?php
/**
 * @package org.carrot-framework
 */

/**
 * 例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSException extends Exception {

	/**
	 * @access public
	 */
	public function __construct () {
		$this->setName(get_class($this));

		switch (count($args = func_get_args())) {
			case 0:
				$message = $this->getName() . 'が発生しました。';
				break;
			case 1:
				$message = $args[0];
				break;
			default:
				foreach ($args as &$arg) {
					$arg = '\'' . str_replace('\'', '\\\'', $arg) . '\'';
				}
				eval('$message = sprintf(' . join(',', $args) . ');');
		}

		parent::__construct($message);
		try {
			BSController::getInstance()->putLog($this->message, $this->getName());
		} catch (Exception $e) {
		}
	}

	/**
	 * 名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		return $this->name;
	}

	/**
	 * 名前を設定
	 *
	 * @access protected
	 * @param string $name 名前
	 */
	protected function setName ($name) {
		$this->name = $name;
	}
}

/* vim:set tabstop=4: */
