<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage log
 */

/**
 * ログマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLogManager implements IteratorAggregate {
	use BSSingleton, BSBasicObject;
	private $loggers;

	/**
	 * @access protected
	 */
	protected function __construct () {
		$this->loggers = new BSArray;
		foreach (BSString::explode(',', BS_LOG_LOGGERS) as $class) {
			$this->register($this->loader->createObject($class, 'Logger'));
		}
	}

	/**
	 * ロガーを登録
	 *
	 * @access public
	 * @param BSLogger $logger ロガー
	 */
	public function register (BSLogger $logger) {
		if ($logger->initialize()) {
			$this->loggers[] = $logger;
		}
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
	 * @param mixed $message ログメッセージ又は例外
	 * @param string $priority 優先順位
	 */
	public function put ($message, $priority = BSLogger::DEFAULT_PRIORITY) {
		if ($message instanceof BSStringFormat) {
			$message = $message->getContents();
		} else if ($message instanceof Exception) {
			$priority = $message->getName();
			$message = $message->getMessage();
		}
		if (is_object($priority)) {
			$priority = get_class($priority);
		}
		foreach ($this as $logger) {
			$logger->put($message, $priority);
		}
	}

	/**
	 * @access public
	 * @return BSIterator イテレータ
	 */
	public function getIterator () {
		return $this->loggers->getIterator();
	}
}

/* vim:set tabstop=4: */
