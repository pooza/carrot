<?php
/**
 * @package org.carrot-framework
 * @subpackage log
 */

/**
 * ログ例外
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSLogException extends BSException {

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
		$this->sendAlert();
	}
}

/* vim:set tabstop=4 ai: */
?>