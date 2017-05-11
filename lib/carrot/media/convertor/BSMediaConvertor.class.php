<?php
/**
 * @package jp.co.b-shock.carrot
 * @subpackage media.convertor
 */

/**
 * 動画の変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMediaConvertor {
	use BSBasicObject;
	protected $name;
	protected $config;
	protected $output;
	protected $constants;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->constants = new BSConstantHandler(
			'FFMPEG_CONVERT_' . ltrim($this->getSuffix(), '.')
		);
		$platform = $this->controller->getPlatform();
		$values = $platform->getConstants(self::getOptions()->getKeys(), $this->constants);

		$this->config = new BSArray;
		foreach ($values as $key => $value) {
			$this->setConfig($key, $value);
		}
	}

	/**
	 * コンバータの名前を返す
	 *
	 * @access public
	 * @return string 名前
	 */
	public function getName () {
		if (!$this->name) {
			if (mb_ereg('^BS(.*)MediaConvertor$', get_class($this), $matches)) {
				$this->name = $matches[1];
			}
		}
		return $this->name;
	}

	/**
	 * 変換後ファイルのサフィックス
	 *
	 * @access public
	 * @return string サフィックス
	 */
	public function getSuffix () {
		return '.' . BSString::toLower($this->getName());
	}

	/**
	 * 変換後のクラス名
	 *
	 * @access public
	 * @return string クラス名
	 * @abstract
	 */
	abstract public function getClass ();

	/**
	 * 変換後ファイルのMIMEタイプ
	 *
	 * @access public
	 * @return string MIMEタイプ
	 */
	public function getType () {
		return BSMIMEType::getType($this->getSuffix());
	}

	/**
	 * 変換して返す
	 *
	 * @access public
	 * @param BSMovieFile $source 変換後ファイル
	 * @return BSMediaFile 変換後ファイル
	 */
	public function execute (BSMediaFile $source) {
		$file = BSFileUtility::createTemporaryFile($this->getSuffix());
		if ($source->getType() == $this->getType()) {
			$duplicated = $source->copyTo($file->getDirectory());
			$duplicated->rename($file->getName());
			$file = $duplicated;
		} else {
			$command = $source->createCommand();
			$command->push('-y', null);
			$command->push('-i', null);
			$command->push($source->getPath());
			foreach ($this->config as $key => $value) {
				$command->push('-' . $key, null);
				$command->push($value);
			}
			$command->push($file->getPath());
			$this->output = $command->getResult()->join("\n");

			if ($size = $file->getSize()) {
				$message = new BSStringFormat('%sを変換しました。(%s)');
				$message[] = $source;
				$message[] = BSNumeric::getBinarySize($size) . 'B';
				BSLogManager::getInstance()->put($message, $this);
			} else {
				throw new BSMediaException($command->getResult()->getIterator()->getLast());
			}
		}
		return BSUtility::executeMethod($this->getClass(), 'search', [$file]);
	}

	/**
	 * 全ての設定値を返す
	 *
	 * @access public
	 * @param string $name 設定値の名前
	 * @param string $value 設定値
	 */
	public function setConfig ($name, $value) {
		$options = self::getOptions();
		if ($option = $options[$name]) {
			$this->config[$option] = $value;
		} else if ($options->isContain($name)) {
			$this->config[$name] = $value;
		}
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数名
	 * @return string 定数値
	 */
	public function getConstant ($name) {
		return $this->constants[$name];
	}

	/**
	 * オプション一式を返す
	 *
	 * @access protected
	 * @return BSArray オプション一式
	 * @static
	 */
	static protected function getOptions () {
		return  new BSArray([
			'video_codec' => 'vcodec',
			'audio_codec' => 'acodec',
			'size' => 's',
			'frame_rate' => 'r',
			'bit_rate' => 'b',
			'max_file_size' => 'fs',
			'padding_top' => 'paddtop',
			'padding_bottom' => 'padbottom',
			'audio_sampling_frequency' => 'ar',
			'strict' => 'strict',
			'vpre' => 'vpre',
			'ss' => 'ss',
			'vframes' => 'vframes',
		]);
	}

	/**
	 * 偶数化
	 *
	 * @access public
	 * @param string $num 対象数値
	 * @return integer 偶数化した数値
	 * @static
	 */
	static public function evenize ($num) {
		return BSNumeric::round($num / 2) * 2;
	}
}

/* vim:set tabstop=4: */
