<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage request
 */

/**
 * コンソールリクエスト
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSConsoleRequest extends BSRequest {
	private $parser;
	static private $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->addOption(BSController::MODULE_ACCESSOR, 'module');
		$this->addOption(BSController::ACTION_ACCESSOR, 'action');
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
			self::$instance = new BSConsoleRequest;
		}
		return self::$instance;
	}

	/**
	 * コマンドラインパーサを返す
	 *
	 * @access private
	 * @return Console_CommandLine コマンドラインパーサ
	 */
	private function getParser () {
		if (!$this->parser) {
			require_once 'Console/CommandLine.php';
			$this->parser = new Console_CommandLine;
		}
		return $this->parser;
	}

	/**
	 * コマンドラインパーサオプションを追加する
	 *
	 * @access public
	 * @param string $name オプション名
	 * @param string $longname 長いオプション名
	 */
	public function addOption ($name, $longname) {
		$params = array(
			'short_name' => '-' . $name,
			'long_name' => '--' . $longname,
		);
		$this->getParser()->addOption($name, $params);
	}

	/**
	 * コマンドラインをパース
	 *
	 * @access public
	 */
	public function parse () {
		$this->clearParameters();
		$this->setParameters($this->getParser()->parse()->options);
	}
}

/* vim:set tabstop=4 ai: */
?>