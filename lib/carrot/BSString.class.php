<?php
/**
 * @package org.carrot-framework
 */

/**
 * 文字列に関するユーティリティ
 *
 * @author 小石達也 <tkoishi@b-shock.co.jp>
 * @version $Id$
 */
class BSString {
	const DETECT_ORDER = 'ascii,jis,utf-8,euc-jp,sjis';

	/**
	 * @access private
	 */
	private function __construct () {
	}

	/**
	 * エンコード変換
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @param string $encodingTo 変換後エンコード
	 * @param string $encodingFrom 変換前エンコード
	 * @return mixed 変換後
	 * @static
	 */
	static public function convertEncoding ($value, $encodingTo = null, $encodingFrom = null) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::convertEncoding($item, $encodingTo, $encodingFrom);
			}
		} else {
			if (!$encodingTo) {
				$encodingTo = 'utf-8';
			}
			if (!$encodingFrom) {
				$encodingFrom = self::DETECT_ORDER;
			}
			if ($encodingFrom != $encodingTo) {
				$value = mb_convert_encoding($value, $encodingTo, $encodingFrom);
			}
		}
		return $value;
	}

	/**
	 * エンコードを返す
	 *
	 * @access public
	 * @param string $str 評価対象の文字列
	 * @return string PHPのエンコード名
	 * @static
	 */
	static public function getEncoding ($str) {
		return strtolower(mb_detect_encoding($str, self::DETECT_ORDER));
	}

	/**
	 * 文字列のサニタイズ
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function sanitize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::sanitize($item);
			}
		} else {
			$value = htmlspecialchars($value, ENT_QUOTES);
		}
		return $value;
	}

	/**
	 * サニタイズされた文字列を元に戻す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function unsanitize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::unsanitize($item);
			}
		} else {
			$value = htmlspecialchars_decode($value, ENT_QUOTES);
		}
		return $value;
	}

	/**
	 * 全角・半角を標準化
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @param string $format 変換の形式
	 * @return mixed 変換後
	 * @static
	 */
	static public function convertKana ($value, $format = 'KVa') {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::convertKana($item, $format);
			}
		} else {
			$value = mb_convert_kana($value, $format, 'utf-8');
		}
		return $value;
	}

	/**
	 * 文字を規定の長さで切り詰める
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @param integer $length 長さ
	 * @param string $suffix サフィックス
	 * @return mixed 変換後
	 * @static
	 */
	static public function truncate ($value, $length, $suffix = '...') {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::truncate($item, $length, $suffix);
			}
		} else if ($length < self::getWidth($value)) {
			$value = self::convertEncoding($value, 'eucjp-win', 'utf-8');
			$value = mb_strcut($value, 0, $length, 'eucjp-win') . $suffix;
			$value = self::convertEncoding($value, 'utf-8', 'eucjp-win');
		}
		return $value;
	}

	/**
	 * キャピタライズされた文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function capitalize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::capitalize($item);
			}
		} else {
			$value = explode('-', $value);
			foreach ($value as &$item) {
				$item = ucfirst(strtolower($item));
			}
			$value = implode('-', $value);
		}
		return $value;
	}

	/**
	 * Camel化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function camelize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::camelize($item);
			}
		} else {
			if ($parts = preg_split('/[_ ]/u', $value)) {
				$dest = strtolower(array_shift($parts));
				foreach ($parts as $part) {
					$dest .= self::capitalize($part);
				}
				$value = $dest;
			}
		}
		return $value;
	}

	/**
	 * Palcal化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function pascalize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::pascalize($item);
			}
		} else {
			$dest = '';
			foreach (preg_split('/[_ ]/u', $value) as $part) {
				$dest .= self::capitalize($part);
			}
			$value = $dest;
		}
		return $value;
	}

	/**
	 * アンダースコア化された文字列を返す
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function underscorize ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::underscorize($item);
			}
		} else {
			foreach (array('/[A-Z][a-z0-9]+/u', '/[A-Z]{2,}/u', '/[A-Z]/u') as $pattern) {
				while (preg_match($pattern, $value, $matches)) {
					$word = $matches[0];
					$value = str_replace($word, '_' . strtolower($word), $value);
				}
			}
			$value = preg_replace('/_+/u', '_', $value);
			$value = preg_replace('/^_/u', '', $value);
			$value = strtolower($value);
		}
		return $value;
	}

	/**
	 * セパレータで分割した配列を返す
	 *
	 * @access public
	 * @param string $separator セパレータ
	 * @param string $str 対象文字列
	 * @return BSArray 結果配列
	 * @static
	 */
	static public function explode ($separator, $str) {
		return new BSArray(explode($separator, $str));
	}

	/**
	 * 半角単位での文字列の幅を返す
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @return integer 半角単位での幅
	 * @static
	 */
	static public function getWidth ($str) {
		return mb_strwidth($str);
	}

	/**
	 * 指定幅で折り畳む
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @param integer $witdh 半角単位での行幅
	 * @return string 変換後の文字列
	 * @static
	 */
	static public function split ($str, $width = 74) {
		$str = self::convertEncoding($str, 'eucjp-win', 'utf-8');

		BSController::includeFile('OME.php');
		mb_internal_encoding('eucjp-win');
		$ome = new OME;
		$ome->setBodyWidth($width);
		$str = @$ome->devideWithLimitingWidth($str);
		mb_internal_encoding('utf-8');

		$str = self::convertEncoding($str, 'utf-8', 'eucjp-win');
		return $str;
	}

	/**
	 * 引用文に整形
	 *
	 * @access public
	 * @param string $str 対象文字列
	 * @param integer $witdh 半角単位での行幅
	 * @param string $prefix 行頭記号
	 * @return string 変換後の文字列
	 * @static
	 */
	static public function cite ($str, $width = 74, $prefix = '> ') {
		$str = self::split($str, $width - self::getWidth($prefix));
		$lines = explode("\n", $str);
		foreach ($lines as &$line) {
			$line = $prefix . $line;
		}
		return implode("\n", $lines);
	}

	/**
	 * HTMLタグを取り除く
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function stripHTMLTags ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::stripHTMLTags($item);
			}
		} else {
			$value = preg_replace('/<\/?[^>]*>/u', '', $value);
		}
		return $value;
	}

	/**
	 * コントロール文字を取り除く
	 *
	 * @access public
	 * @param mixed $value 変換対象の文字列又は配列
	 * @return mixed 変換後
	 * @static
	 */
	static public function stripControlCharacters ($value) {
		if (BSArray::isArray($value)) {
			foreach ($value as $key => $item) {
				$value[$key] = self::stripControlCharacters($item);
			}
		} else {
			$value = preg_replace('/[[:cntrl:]]/u', '', $value);
		}
		return $value;
	}

	/**
	 * 文字列に変換
	 *
	 * @access public
	 * @param $mixed[] $value 変換対象
	 * @param string $fieldGlue キーと値の間に入る文字列
	 * @param string $elementGlue 要素の間に入る文字列
	 * @return string 変換後の文字列
	 * @static
	 */
	static public function toString ($value, $fieldGlue = '', $elementGlue = ',') {
		if (!BSArray::isArray($value)) {
			return $value;
		}

		$elements = new BSArray;
		foreach ($value as $key => $element) {
			$elements[] = $key . $fieldGlue . $element;
		}
		return $elements->join($elementGlue);
	}

	/**
	 * よく使うエンコード名を返す
	 *
	 * @access public
	 * @return BSArray エンコード名の配列
	 * @static
	 */
	static public function getEncodings () {
		return new BSArray(array(
			'utf-8',
			'eucjp-win',
			'sjis-win',
			'iso-2022-jp',
		));
	}
}

/* vim:set tabstop=4 ai: */
?>