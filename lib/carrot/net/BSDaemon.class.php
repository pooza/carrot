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
 */
class BSDaemon extends NS_Line_Input_Connection_Handler {
	private $attributes;

	/**
	 * 開始
	 *
	 * @access public
	 */
	public function start () {
		Nanoserv::New_Timer($this->getIdleTimeSeconds(), array($this, 'on_Idle'));
		do {
			BSController::getInstance()->removeAttribute($this);
			$status = array(
				'port' => BSNumeric::getRandom(48557, 49150),
				'pid' => BSProcess::getCurrentID(),
			);
			BSController::getInstance()->setAttribute($this, $status);

			$listener = Nanoserv::New_Listener(
				'tcp://0.0.0.0:' . $this->getAttribute('port'),
				get_class($this)
			);
		} while (!$listener->activate());

		if (!$this->initialize()) {
			throw new BSNetException('%sの初期化中にエラーが発生しました。', $this);
		}

		$message = sprintf(
			'%s（ポート:%d, PID:%d）を開始しました。',
			$this,
			$this->getAttribute('port'),
			$this->getAttribute('pid')
		);
		BSController::getInstance()->putLog($message, get_class($this));
		Nanoserv::Run();
	}

	/**
	 * 初期化
	 *
	 * @access protected
	 * @return boolean 正常終了ならばTrue
	 */
	protected function initialize () {
		return true;
	}

	/**
	 * 属性を全て返す
	 *
	 * @access public
	 * @return BSArray 属性
	 */
	public function getAttributes () {
		if (!$this->attributes) {
			$this->attributes = new BSArray(BSController::getInstance()->getAttribute($this));
		}
		return $this->attributes;
	}

	/**
	 * 属性値を返す
	 *
	 * @access public
	 * @param string $name 属性名
	 * @return mixed 属性値
	 */
	public function getAttribute ($name) {
		return $this->getAttributes()->getParameter($name);
	}

	/**
	 * 動作中か？
	 *
	 * @access public
	 * @return boolean 動作中ならTrue
	 */
	public function isActive () {
		return BSProcess::isExists($this->getAttribute('pid'));
	}

	/**
	 * 受信時処理
	 *
	 * @access public
	 * @param string $line 受信文字列
	 */
	public function on_Read_Line ($line) {
		$line = trim($line);
		if (BS_DEBUG) {
			BSController::getInstance()->putLog('request: ' . $line, get_class($this));
		}
		$this->onGetLine($line);
	}

	/**
	 * アイドル時処理
	 *
	 * @access public
	 */
	public function on_Idle () {
		if (BSNumeric::isZero($seconds = $this->getIdleTimeSeconds())) {
			return;
		}
		$this->onIdle();
		Nanoserv::New_Timer($seconds, array($this, 'on_Idle'));
	}

	/**
	 * 受信時
	 *
	 * @access public
	 * @param string $line 受信文字列
	 */
	public function onGetLine ($line) {
		switch ($line) {
			case null;
				break;
			case '/QUIT':
			case '/EXIT':
				$this->disconnect();
				exit;
			default:
				break;
		}
	}

	/**
	 * アイドル時
	 *
	 * @access public
	 */
	public function onIdle () {
	}

	/**
	 * アイドル処理の周期を返す
	 *
	 * @access public
	 * @return integer アイドル処理の周期を秒単位で（何もしないなら0を返す）
	 */
	public function getIdleTimeSeconds () {
		return 0;
	}

	/**
	 * @access public
	 * @return string 基本情報
	 */
	public function __toString () {
		return sprintf('デーモン "%s"', get_class($this));
	}
}

/* vim:set tabstop=4: */
