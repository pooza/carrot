<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.image.renderer
 */

/**
 * ImageMagick 7.x画像レンダラー
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 */
class BSImagemagick7Image extends BSImage {

	/**
	 * @access public
	 */
	public function __construct () {
		if (!$this->createCommand()->isExists()) {
			throw new BSImageException('ImageMagick 7.xがインストールされていません。');
		}
	}

	/**
	 * convertコマンドを生成して返す
	 *
	 * @access public
	 * @return BSCommandLine convertコマンド
	 */
	public function createCommand ($command = 'bin/convert') {
		$command = new BSCommandLine($command);
		$command->setDirectory(BSFileUtility::getDirectory('image_magick'));
		return $command;
	}

	/**
	 * サイズ変更
	 *
	 * @access public
	 * @param integer $width 幅
	 * @param integer $height 高さ
	 */
	public function resize ($width, $height) {
		$suffixes = BSImage::getSuffixes();
		$src = BSFileUtility::createTemporaryFile($suffixes[$this->getType()]);
		$src->setContents($this->getContents());
		$dest = BSFileUtility::createTemporaryFile($suffixes[BS_IMAGE_THUMBNAIL_TYPE]);

		$command = $this->createCommand();
		$command->push($src->getPath());
		$command->push('-resize');
		$command->push($width . 'x' . $height);
		$command->push('-size');
		$command->push($width . 'x' . $height);
		$command->push('xc:' . $this->getBackgroundColor()->getContents());
		$command->push('+swap');
		$command->push('-gravity');
		$command->push('center');
		$command->push('-composite');
		$command->push($dest->getPath());
		$command->execute();

		$this->setImage($dest->getContents());
		$src->delete();
		$dest->delete();
	}
}

