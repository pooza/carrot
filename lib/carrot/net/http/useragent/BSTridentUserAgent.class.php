<?php
/**
 * @package org.carrot-framework
 * @subpackage net.http.useragent
 */

/**
 * Tridentユーザーエージェント
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSTridentUserAgent extends BSUserAgent {
	const DEFAULT_NAME = 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; Trident/4.0)';
	const ACCESSOR = 'force_trident';

	/**
	 * @access protected
	 * @param string $name ユーザーエージェント名
	 */
	protected function __construct ($name = null) {
		parent::__construct($name);

		// SSL領域で、 Cache-Control ヘッダを正しく処理できない。
		$this->bugs['cache_control'] = true;

		// wmode="transparent" を正しく処理できない。
		$this->bugs['object_wmode'] = (8 < $this->getVersion());

		// 全角スペース等だけで構成されたブロック要素が、widthを持てないケースがある。
		$this->bugs['block_width'] = (8 < $this->getVersion());

		$this->supports['html5_audio'] = (8 < $this->getVersion());
		$this->supports['html5_video'] = (8 < $this->getVersion());
		$this->supports['html5_video_h264'] = (8 < $this->getVersion());
		$this->supports['flash'] = true;
		$this->supports['cookie'] = true;
		$this->supports['attach_file'] = true;
		$this['is_ie' . (int)$this->getVersion()] = true;
	}

	/**
	 * ダウンロード用にエンコードされたファイル名を返す
	 *
	 * @access public
	 * @param string $name ファイル名
	 * @return string エンコード済みファイル名
	 */
	public function encodeFileName ($name) {
		$name = BSURL::encode($name);
		$name = str_replace('+', ' ', $name);
		return BSString::sanitize($name);
	}

	/**
	 * ダイジェストを返す
	 *
	 * @access public
	 * @return string ダイジェスト
	 */
	public function digest () {
		if (!$this->digest) {
			$this->digest = BSCrypt::digest(array(
				__CLASS__,
				$this->getVersion(),
				$this->isSmartPhone(),
				$this->isTablet(),
			));
		}
		return $this->digest;
	}

	/**
	 * バージョンを返す
	 *
	 * 取得できないケースがある為、IEとしてのバージョンで代用。
	 *
	 * @access public
	 * @return string バージョン
	 */
	public function getVersion () {
		if (!$this['version']) {
			if (mb_ereg('MSIE ([.[:digit:]]+);', $this->getName(), $matches)) {
				$this['version'] = $matches[1];
			}
			if (mb_ereg('Trident', $this->getName(), $matches)) {
				$this['version'] = 10;
			}
		}
		return $this['version'];
	}

	/**
	 * レガシー環境/旧機種か？
	 *
	 * @access public
	 * @return boolean レガシーならばTrue
	 */
	public function isLegacy () {
		return $this->getVersion() < 6; // IE6未満
	}

	/**
	 * 一致すべきパターンを返す
	 *
	 * @access public
	 * @return string パターン
	 */
	public function getPattern () {
		return '(MSIE|Trident)';
	}
}

/* vim:set tabstop=4: */
