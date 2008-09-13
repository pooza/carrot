<?php
/**
 * @package org.carrot-framework
 * @subpackage log
 */

/**
 * ログマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSLogManager implements IteratorAggregate {
	private $loggers;
	static private $instance;
	const DEFAULT_LOGGER_CLASSES = 'BSSystemLogger';

	/**
	 * @access private
	 */
	private function __construct () {
		$this->loggers = new BSArray;
		if (!$classes = BSController::getInstance()->getConstant('LOG_CLASSES')) {
			$classes = self::DEFAULT_LOGGER_CLASSES;
		}
		foreach (BSString::explode(',', $classes) as $class) {
			$this->register(new $class);
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSLogManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSLogManager;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * ロガーを登録
	 *
	 * @access public
	 * @param BSLogger $logger ロガー
	 */
	public function register (BSLogger $logger) {
		$this->loggers[] = $logger;
	}

	/**
	 * 最優先のロガーを返す
	 *
	 * @access public
	 * @param BSLogger $logger ロガー
	 */
	public function getPrimaryLogger () {
		return $this->getIterator()->getFirst();
	}

	/**
	 * ログを出力
	 *
	 * @access public
	 * @param string $message ログメッセージ
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = BSLogger::DEFAULT_PRIORITY) {
		foreach ($this as $logger) {
			$logger->put($message, $priority);
		}
	}

	/**
	 * イテレータを返す
	 *
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->loggers->getIterator();
	}
}

/* vim:set tabstop=4 ai: */
?>