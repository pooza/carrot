<?php
/**
 * @package org.carrot-framework
 * @subpackage media.convertor
 */

/**
 * 動画の変換
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @abstract
 */
abstract class BSMediaConvertor {
	protected $name;
	protected $config;
	protected $output;

	/**
	 * @access public
	 */
	public function __construct () {
		$this->config = new BSArray;
		foreach (self::getOptions() as $name => $option) {
			if ($value = $this->getConstant($name)) {
				$this->config[$option] = $value;
			}
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
		if (!$this->isForceExecutable() && ($source->getType() == $this->getType())) {
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
				$message = new BSStringFormat('%sを変換しました。 (%sB)');
				$message[] = $source;
				$message[] = BSNumeric::getBinarySize($size);
				BSLogManager::getInstance()->put($message, $this);
			} else {
				throw new BSMediaException($source . 'の変換に失敗しました。');
			}
		}
		return BSUtility::executeMethod($this->getClass(), 'search', array($file));
	}

	/**
	 * 強制的に実行するか？
	 *
	 * 変換前後でMIMEタイプが一致する時は、デフォルトでは変換を行わず、単にコピーする。
	 *
	 * @access protected
	 * @return boolean 強制的に実行するならTrue
	 */
	protected function isForceExecutable () {
		return !!$this->getConstant('force');
	}

	/**
	 * 全ての設定値を返す
	 *
	 * @access public
	 * @param string $name 設定値の名前
	 * @param string $value 設定値
	 */
	public function setConfig ($name, $value) {
		if ($option = self::getOptions()->getParameter($name)) {
			$this->config[$option] = $value;
		} else if (self::getOptions()->isContain($name)) {
			$this->config[$name] = $value;
		}
	}

	/**
	 * 定数を返す
	 *
	 * @access public
	 * @param string $name 定数名
	 * @param BSPlatform $platform 対象プラットフォーム
	 * @return string 定数値
	 */
	public function getConstant ($name, $platform = null) {
		if (!$platform) {
			$platform = BSController::getInstance()->getPlatform();
		}
		return $platform->getMediaConverterConstant($this->getName() . '_' . $name);
	}

	/**
	 * オプション一式を返す
	 *
	 * @access protected
	 * @return BSArray オプション一式
	 * @static
	 */
	static protected function getOptions () {
		return  new BSArray(array(
			'video_codec' => 'vcodec',
			'audio_codec' => 'acodec',
			'size' => 's',
			'frame_rate' => 'r',
			'bit_rate' => 'b',
			'max_file_size' => 'fs',
			'padding_top' => 'paddtop',
			'padding_bottom' => 'padbottom',
			'strict' => 'strict',

			// libmp3lame
			'audio_sampling' => 'ar',

			// libx264
			'gop_length' => 'g',
			'qp_curve_compression' => 'qcomp',
			'quantizer_min' => 'qmin',
			'quantizer_max' => 'qmax',
			'max_qp_step' => 'qdiff',
			'subq' => 'subq',
			'me_range' => 'me_range',
			'i_qfactor' => 'i_qfactor',
		));
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
