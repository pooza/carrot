<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage image
 */

/**
 * QRCodeレンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @copyright (c)b-shock. co., ltd.
 * @version $Id: BSQRCode.class.php 101 2007-11-24 09:37:40Z pooza $
 */
class BSQRCode implements BSImageRenderer {
	private $image;
	private $type;
	private $data;
	private $error;
	private $engine;

	/**
	 * コンストラクタ
	 *
	 * @access public
	 */
	public function __construct () {
		if (!extension_loaded('qr')) {
			throw new BSImageException('"qr"モジュールがロードされていません。');
		}
		$this->engine = new QRCode;
		$this->engine->setMagnify(3);
		$this->setType('image/gif');
	}

	/**
	 * 未定義メソッドの呼び出し
	 *
	 * @access public
	 * @param string $method メソッド名
	 * @param mixed[] $values 引数
	 */
	public function __call ($method, $values) {
		if (!method_exists($this->engine, $method)) {
			throw new BSException('仮想メソッド"%s"は未定義です。', $method);
		}

		// 処理をエンジンに委譲
		$args = array();
		for ($i = 0 ; $i < count($values) ; $i ++) {
			$args[] = '$values[' . $i . ']';
		}
		eval(sprintf('return $this->engine->%s(%s);', $method, implode(', ', $args)));
	}

	/**
	 * エンコード対象データを返す
	 *
	 * @access public
	 * @return string エンコード対象データ
	 */
	public function getData () {
		return $this->data;
	}

	/**
	 * エンコード対象データを設定
	 *
	 * @access public
	 * @param string $data エンコード対象データ
	 */
	public function setData ($data) {
		if ($this->data) {
			throw new BSImageException('エンコード対象は設定済みです。');
		} else {
			$this->data = $data;
			$this->engine->addData($data);
			$this->engine->finalize();
		}
	}

	/**
	 * メディアタイプを返す
	 *
	 * @access public
	 * @return string メディアタイプ
	 */
	public function getType () {
		return $this->type;
	}

	/**
	 * メディアタイプを設定
	 *
	 * @access public
	 * @param string $type メディアタイプ
	 */
	public function setType ($type) {
		switch ($this->type = $type) {
			case 'image/jpeg':
				$this->engine->setFormat(QR_FMT_JPEG);
				break;
			case 'image/gif':
				$this->engine->setFormat(QR_FMT_GIF);
				break;
			case 'image/png':
				$this->engine->setFormat(QR_FMT_PNG);
				break;
			default:
				throw new BSImageException('メディアタイプ"%s"が正しくありません。', $type);
		}
	}

	/**
	 * GDイメージリソースを返す
	 *
	 * @access public
	 * @return resource GDイメージリソース
	 */
	public function getImage () {
		if (!$this->image && $this->getData()) {
			$this->image = $this->engine->getImageResource();
		}
		return $this->image;
	}

	/**
	 * 幅を返す
	 *
	 * @access public
	 * @return integer 幅
	 */
	public function getWidth () {
		return imagesx($this->getImage());
	}

	/**
	 * 高さを返す
	 *
	 * @access public
	 * @return integer 高さ
	 */
	public function getHeight () {
		return imagesy($this->getImage());
	}

	/**
	 * 送信内容を返す
	 *
	 * @access public
	 * @return string 送信内容
	 */
	public function getContents () {
		ob_start();
		$this->engine->outputSymbol();
		$contents = ob_get_contents();
		ob_end_clean();
		return $contents;
	}

	/**
	 * 出力内容のサイズを返す
	 *
	 * @access public
	 * @return integer サイズ
	 */
	public function getSize () {
		return strlen($this->getContents());
	}

	/**
	 * 出力可能か？
	 *
	 * @access public
	 * @return boolean 出力可能ならTrue
	 */
	public function validate () {
		if (!$this->getData()) {
			$this->error = 'データが未定義です。';
			return false;
		}
		return true;
	}

	/**
	 * エラーメッセージを返す
	 *
	 * @access public
	 * @return string エラーメッセージ
	 */
	public function getError () {
		return $this->error;
	}
}

/* vim:set tabstop=4 ai: */
?>