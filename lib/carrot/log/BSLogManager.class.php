<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * ログマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSLogManager implements IteratorAggregate {
	private $loggers;
	static private $instance;
	const DEFAULT_LOGGER_CLASSES = 'BSFileLogger,BSSystemLogger';

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->loggers = new BSArray;
		if (!$classes = BSConstantHandler::getInstance()->getParameter('LOG_LOGGER_CLASS')) {
			$classes = self::DEFAULT_LOGGER_CLASSES;
		}
		foreach (explode(',', $classes) as $class) {
			$this->register(new $class);
		}
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSValidatorManager インスタンス
	 * @static
	 */
	static public function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSLogManager;
		}
		return self::$instance;
	}

	/**
	 * ディープコピーを行う
	 *
	 * @access public
	 */
	public function __clone () {
		throw new BSException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * ロガーを登録する
	 *
	 * @access public
	 * @param BSLogger $logger ロガー
	 */
	public function register (BSLogger $logger) {
		$this->loggers[] = $logger;
	}

	/**
	 * ログを出力する
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