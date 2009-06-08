<?php
/**
 * @package org.carrot-framework
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSConsoleRequest extends BSRequest {
	private $options;
	static private $instance;

	/**
	 * @access private
	 */
	private function __construct () {
		$this->method = 'GET';
		$this->options = new BSArray;
		$this->addOption(BSController::MODULE_ACCESSOR);
		$this->addOption(BSController::ACTION_ACCESSOR);
		$this->parse();
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSConsoleRequest インスタンス
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
		throw new BSSingletonException('"%s"はコピー出来ません。', __CLASS__);
	}

	/**
	 * コマンドラインパーサオプションを追加
	 *
	 * @access public
	 * @param string $name オプション名
	 */
	public function addOption ($name) {
		$this->options[$name] = array(
			'name' => $name,
		);
	}

	/**
	 * コマンドラインをパース
	 *
	 * @access public
	 */
	public function parse () {
		$config = new BSArray;
		foreach ($this->options as $option) {
			$config[] = $option['name'] . ':';
		}
		$config = $config->join('');

		$this->clearParameters();
		$this->setParameters(getopt($config));
	}

	/**
	 * コマンドライン環境か？
	 *
	 * @access public
	 * @return boolean コマンドライン環境ならTrue
	 */
	public function isCLI () {
		return true;
	}
}

/* vim:set tabstop=4: */
