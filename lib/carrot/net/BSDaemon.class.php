<?php
/**
 * @package org.carrot-framework
 * @subpackage net
 */

BSUtility::includeFile('nanoserv/nanoserv.php');
BSUtility::includeFile('nanoserv/handlers/NS_Line_Input_Connection_Handler.php');

/**
 * デーモン
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 * @abstract
 */
abstract class BSDaemon extends NS_Line_Input_Connection_Handler {

	/**
	 * @access public
	 */
	public function __construct () {
		Nanoserv::New_Timer($this->getIdleTimeSeconds(), array($this, 'on_Idle'));
	}

	/**
	 * 初期化
	 *
	 * @access protected
	 * @param string サービス名
	 * @return integer[] ポートとPIDを含んだ配列
	 */
	static protected function initialize ($name) {
		BSController::getInstance()->removeAttribute($name);
		$status = array(
			'port' => BSNumeric::getRandom(48557, 49150),
			'pid' => BSProcess::getCurrentID(),
		);
		BSController::getInstance()->setAttribute($name, $status);
		return $status;
	}

	/**
	 * 受信時処理
	 *
	 * @access public
	 * @param string $str 受信文字列
	 */
	public function on_Read_Line ($str) {
		$this->onGetLine(trim($str));
	}

	/**
	 * アイドル時処理
	 *
	 * @access public
	 */
	public function on_Idle () {
		if (!$seconds = $this->getIdleTimeSeconds()) {
			return;
		}
		$this->onIdle();
		Nanoserv::New_Timer($seconds, array($this, 'on_Idle'));
	}

	/**
	 * 受信時
	 *
	 * @access public
	 * @param string $str 受信文字列
	 * @abstract
	 */
	abstract public function onGetLine ($str);

	/**
	 * アイドル時
	 *
	 * @access public
	 * @abstract
	 */
	abstract public function onIdle ();

	/**
	 * アイドル処理の周期を返す
	 *
	 * @access protected
	 * @return integer アイドル処理の周期を秒単位で（何もしないなら0を返す）
	 */
	protected function getIdleTimeSeconds () {
		return 300;
	}
}

/* vim:set tabstop=4 ai: */
?>