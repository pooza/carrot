<?php
/**
 * @package jp.co.b-shock.carrot
 */

/**
 * メール送信型デバッグエンジン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id$
 */
class BSDebug {
	private $smtp;
	private $count = 0;
	private $session;
	private static $instance;

	/**
	 * コンストラクタ
	 *
	 * @access private
	 */
	private function __construct () {
		$this->session = BSDate::getNow('H:i:s');

		$this->smtp = new BSSMTP();
	}

	/**
	 * デストラクタ
	 *
	 * @access public
	 */
	public function __destruct () {
		$this->smtp->close();
	}

	/**
	 * シングルトンインスタンスを返す
	 *
	 * @access public
	 * @return BSDebug インスタンス
	 * @static
	 */
	public static function getInstance () {
		if (!self::$instance) {
			self::$instance = new BSDebug();
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
	 * メールを送信
	 *
	 * @access public
	 * @param mixed $var 出力対象
	 * @param string $label ラベル
	 */
	public function put ($var, $label = 'None') {
		$this->count ++;

		$this->smtp->setSubject(
			sprintf(
				'session:%s label:%s count:%d',
				$this->session,
				$label,
				$this->count
			)
		);
		$this->smtp->setBody(print_r($var, true));
		$this->smtp->send();
	}

	/**
	 * ブラウザに出力
	 *
	 * @access public
	 * @param mixed $var 出力対象
	 * @static
	 */
	public static function pr ($var) {
		BSController::getInstance()->sendHeader('Content-Type: text/html; charset=utf-8');

		if (extension_loaded('xdebug')) {
			var_dump($var);
		} else {
			print("<pre>\n");
			print_r($var);
			print("</pre>\n");
		}
	}
}

/* vim:set tabstop=4 ai: */
?>