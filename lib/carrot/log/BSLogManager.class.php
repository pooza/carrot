<?php
/**
 * @package org.carrot-framework
 * @subpackage log
 */

/**
 * ログマネージャ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSLogManager implements IteratorAggregate {
	private $loggers;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->loggers = new BSArray;
		foreach (BSString::explode(',', BS_LOG_LOGGERS) as $class) {
			$this->register(BSLoader::getInstance()->createObject($class, 'Logger'));
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
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * @access public
	 */
	public function __clone () {
		throw new BadFunctionCallException(__CLASS__ . 'はコピーできません。');
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

	/**
	 * メッセージを整形
	 *
	 * 初期化中のエラーでログが吐かれることも想定し、標準関数のみで実装。
	 *
	 * @access public
	 * @param string $message メッセージ
	 * @param string $priority 優先順位
	 * @return string 整形済みメッセージ
	 * @static
	 */
	static public function formatMessage ($message, $priority) {
		foreach (array('HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR') as $key) {
			if (isset($_SERVER[$key]) && ($value = $_SERVER[$key])) {
				try {
					$parts = mb_split('[:,]', $value);
					$host = trim($parts[0]);
				} catch (Exception $e) {
					$host = $value;
				}
				$message = array(
					'[' . date('Y-m-d H:i:s') . ']',
					'[' . $host . ']', 
					'[' . $priority . ']',
					$message,
				);
				return implode(' ', $message);
			}
		}
	}
}

/* vim:set tabstop=4: */
