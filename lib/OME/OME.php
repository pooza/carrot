<?php
###########OME.php/The character set of this file is EUC-JP################
#
#  OME( Open Mail Envrionment ) for PHP   http://mac-ome.jp
#  by Masayuki Nii ( msyk@msyk.net )
#
#  このクラスは、日本語での正しいメール送信を行うために作ったものです。

class OME	{

var $body='';
var $subject='';
var $toField='';
var $ccField='';
var $bccField='';
var $fromField='';
var $extHeaders='';
var $errorMessage='';
var $sendmailParam='';

/*	エラーメッセージを取得する。エラーメッセージはEUCの日本語。
	このクラスの多くの関数は、戻り値がbooleanとなっていて、それをもとにエラーかどうかを判別する。
	falseである場合、この関数を使ってエラーメッセージを取得できる。
*/
function getErrorMessage()	{
	return $this->errorMessage;
}

/*	メールの本文を設定する。既存の本文は置き換えられる。
*/
function setBody($str)	{
	$this->body = $str;	return true;
}

/*	メールの本文を追加する。既存の本文の後に追加する
*/
function appendBody($str)	{
	$this->body .= $str;	return true;
}

/*	メールの件名を設定する。
*/
function setSubject($str)	{
	$this->subject = $str;	return true;
}

/*	追加のヘッダを設定する。

	単に追加するので、基本的にはFrom等は含まれないとする。
	複数のヘッダは、\nで区切って追加する。
	エンコーディング等は行わず、このまま送信するので、日本語を含める場合など
	では、自分でエンコードをする必要がある。
*/
function setExtraHeader($field, $value)	{
	$this->extHeaders = "$fields: $value\n";	return true;
}

/*	メールアドレスが正しい形式かどうかを判断する	*/
function checkEmail($address)	{
	if( ! eregi ("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]+$", $address) )	{
		$this->errorMessage = "アドレス“#address”は正しくないメールアドレスです。";
		return false;
	} else {
		return true;
	}
}

/*	Fromフィールドを設定する。
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
*/
function setFromField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )	{
			$this->fromField = $address;
			$this->sendmailParam = "-f$address";
		}
		else	{
			$this->fromField = "$name <$address>";
			$this->sendmailParam = "-f$address";
		}
		return true;
	}
	return false;
}

/*	Toフィールドを設定する。すでに設定されていれば上書きされ、この引数の定義だけが残る
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは設定されない）、そうでなければtrue
*/
function setToField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$this->toField = "$address";
		else
			$this->toField = "$name <$address>";
		return true;
	}
	return false;
}

/*	Toフィールドに追加する。
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは追加されない）、そうでなければtrue
*/
function appendToField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$appendString = "$address";
		else
			$appendString = "$name <$address>";
		if ( $this->toField == '' )
			$this->toField = $appendString;
		else
			$this->toField .= ", $appendString";
		return true;
	}
	return false;
}

/*	Ccフィールドを設定する。すでに設定されていれば上書きされ、この引数の定義だけが残る
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは設定されない）、そうでなければtrue
*/
function setCcField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$this->ccField = "$address";
		else
			$this->ccField = "$name <$address>";
		return true;
	}
	return false;
}

/*	Ccフィールドに追加する。
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは追加されない）、そうでなければtrue
*/
function appendCcField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$appendString = "$address";
		else
			$appendString = "$name <$address>";
		if ( $this->ccField == '' )
			$this->ccField = $appendString;
		else
			$this->ccField .= ", $appendString";
		return true;
	}
	return false;
}

/*	Bccフィールドを設定する。すでに設定されていれば上書きされ、この引数の定義だけが残る
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは設定されない）、そうでなければtrue
*/
function setBccField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$this->bccField = "$address";
		else
			$this->bccField = "$name <$address>";
		return true;
	}
	return false;
}

/*	Bccフィールドに追加する。
	$address: 送信者のアドレス
	$name: 送信者名（日本語などはEUC-JPであればそのまま指定可能）
	戻り値: メールアドレスを調べて不正ならfalse（アドレスは追加されない）、そうでなければtrue
*/
function appendBccField($address, $name='')	{
	if ( $this->checkEmail($address) )	{
		if ( $name == '' )
			$appendString = "$address";
		else
			$appendString = "$name <$address>";
		if ( $this->bccField == '' )
			$this->bccField = $appendString;
		else
			$this->bccField .= ", $appendString";
		return true;
	}
	return false;
}

var $tmpContents = '';

/*	指定したファイルをテンプレートとして読み込む。
	たとえば、同一のディレクトリにあるファイルなら、ファイル名だけを記述すればよい。
	エラー要因：ファイルがない
*/
function setTemplateAsFile($tfile)	{
	$fileContensArray = file( $tfile );
	if ( $fileContensArray )	{
		$this->tmpContents = implode ( '', $fileContensArray );
		return true;
	}
	$this->errorMessage = "テンプレートファイルが存在しません。";
	return false;
}

/*	文字列そのものをテンプレートして設定する。
*/
function setTemplateAsString($str)	{
	$this->tmpContents = $str;
	return true;
}

/*	テンプレートに引数の配列の内容を差し込み、それを本文とする。既存の本文は上書きされる。
	テンプレート中の「@@1@@」が、$ar[0]の文字列と置き換わる。
	テンプレート中の「@@2@@」が、$ar[1]の文字列と置き換わる。といった具合に置換する。
	たとえば、配列の要素が5の場合、「@@6@@」や「@@7@@」などがテンプレート中に残るが、
	これらは差し込みをしてから強制的に削除される。強制削除があった場合にはfalseを戻すが、
	それでも差し込み自体は行われている。
*/
function insertToTemplate($ar)	{
	$counter = 1;
	foreach ( $ar as $aItem )	{
		$this->tmpContents = str_replace( "@@$counter@@", $aItem, $this->tmpContents );
		$counter += 1;
	}
	if ( ! ereg( '@@[0-9]*@@', $tmpContents ) )	{
		$this->body = ereg_replace('@@[0-9]*@@', '', $this->tmpContents);
		$this->errorMessage = '差し込みテンプレートに余分が置き換え文字列（@@数字@@）がありましたが、削除しました。';
		return false;
	}
	$this->body = $this->tmpContents;
	return true;
}

var $bodyWidth = 74;

/*	本文の自動改行のバイト数を設定する。初期値は74になっている。0を指定すると自動改行しない。	*/
function setBodyWidth($bytes)	{
	$this->bodyWidth = $bytes;
	return true;
}

function checkControlCodeNothing( $str )	{
	return ereg('[[:cntrl:]]', $str);
}

/*	メールを送信する。
	To、Cc、Bccのデータにコントロールコードが入っていると危険なので、その場合はエラーを報告し
	送信はしないものとする。
*/
function send()	{
	if ( $this->checkControlCodeNothing ( $this->toField ) )	{
		$this->errorMessage = '宛先の情報にコントロールコードが含まれています。';
		return false;
	}
	if ( $this->checkControlCodeNothing ( $this->ccField ) )	{
		$this->errorMessage = '宛先の情報にコントロールコードが含まれています。';
		return false;
	}
	if ( $this->checkControlCodeNothing ( $this->bccField ) )	{
		$this->errorMessage = '宛先の情報にコントロールコードが含まれています。';
		return false;
	}
	$headerField = "X-Mailer: Open Mail Envrionment for PHP (http://mac-ome.jp/)\n";
	$headerField .= "Content-Type: text/plain; charset=ISO-2022-JP\n";
	if ( $this->fromField != '' )
		$headerField .= "From: $this->fromField\n";
	if ( $this->ccField != '' )
		$headerField .= "Cc: $this->ccField\n";
	if ( $this->bccField != '' )
		$headerField .= "Bcc: $this->bccField\n";
	if (  $this->extHeaders != '' )
		$headerField .= $this->extHeaders;

/*	$resultMail = mail(
		rtrim($this->header_base64_encode( $this->toField, False )),
		rtrim($this->header_base64_encode( $this->subject, true )),
		mb_convert_encoding( 
			$this->devideWithLimitingWidth( $this->body ), 'ISO-2022-JP' ),
		$this->header_base64_encode( $headerField, True ),
		$this->sendmailParam );
*/	$resultMail = mail(
		rtrim($this->header_base64_encode( $this->toField, False )),
		rtrim($this->header_base64_encode( $this->subject, true )),
		mb_convert_encoding( 
			$this->devideWithLimitingWidth( $this->body ), 'ISO-2022-JP' ),
		$this->header_base64_encode( $headerField, True ) );
	return $resultMail;
}

function devideWithLimitingWidth($str)    {
	if ( $this->bodyWidth == 0 )
		return $str;
	$newLine = "\n";
	$strLength = mb_strlen($str);
	$devidedStr = mb_substr( $str, $pos, 1 );
	$beforeChar = $devidedStr;
	if( $devidedStr == $newLine )
	    $byteLength = 0;
	else
	    $byteLength = strlen( $devidedStr );
	for( $pos = 1 ; $pos < $strLength ; $pos++){
	    $posChar = mb_substr( $str, $pos, 1 );
	    if( $posChar == $newLine )
	        $byteLength = 0;
	    else  {
	        if(        ( $byteLength >= $this->bodyWidth )
	               &&  ! $this->isInhibitLineTopChar( $posChar )
	               &&  ! $this->isInhibitLineEndChar( $beforeChar ) ) {
	    
	            if(        (    $this->isJapanese( $posChar )
	                         && ! $this->isSpace( $posChar )     )
	                    || (    $this->isJapanese( $beforeChar )
	                         && $this->isWordElement( $posChar ) )
	                    || (    ! $this->isWordElement( $beforeChar ) 
	                         && $this->isWordElement( $posChar ) ) ) {
	    
	                $devidedStr .= $newLine;
	                $byteLength = 0;
	            }       // Endo of if
	        }
	        $byteLength += strlen( $posChar );
	    }
	    $devidedStr .= $posChar ;
	    $beforeChar = $posChar;
	}
	return $devidedStr;
}    // End of function devideWithLimitingWidth()

function isSpace( $str )    {
	switch( $str )    {
	    case " ":
	    case "｡｡":    return True;
	}       // Endo of switch
	return False;
}    // End of isSpace()
	
function isWordElement( $str )    {
	if ( $this->isSpace( $str ) )    return False;
	$cCode = ord( $str );
	if ( ( $cCode >=0x30 ) && ($cCode <= 0x39) )    return True;
	if ( ( $cCode >=0x41 ) && ($cCode <= 0x5A) )    return True;
	if ( ( $cCode >=0x61 ) && ($cCode <= 0x7A) )    return True;
	switch( $str )    {
	    case "'":    return True;
	}       // Endo of switch
	return False;
}    // End of function isWordElement()

function isJapanese( $str )    {
	$cCode = ord( $str );
	if ( $cCode >=0x80 )    return True;
	return False;
}    // End of function isJapanese()

function isInhibitLineTopChar( $str )    {
	switch( $str )    {
	    case ')':    case ']':    case '}':     case '）':    case '】':
	    case '”':   case '］':   case '」':    case '』':    case '〕':
	    case '｝':   case '〉':   case '》':    case "’":    case '”':
	    case ':':    case ';':    case '!':     case '.':     case '?': 
	    case '。':   case '、':   case '，':    case '…':    case '‥':
	    case '．':   case '：':   case '；':    case '！':    case '？':
	        return True;
	}       // Endo of switch
	return False;
}    // End of function isInhibitLineTopChar

function isInhibitLineEndChar( $str )    {
	switch( $str )    {
	    case '(':     case '[':     case '{':     case '（':    case '“':
	    case '【':    case '［':    case '『':    case '「':    case '〔':
	    case '｛':    case '〈':    case '《':    case "‘":
	        return True;
	}       // Endo of switch
	return False;
}    // End of function isInhibitLineEndChar

function header_base64_encode( $str, $isSeparateLine )    {
	$strLen = mb_strlen($str);
	$encodedString = '';
	$substring = '';
	$beforeIsMBChar = False;
	$isFirstLine = True;
	for ( $i = 0 ; $i <= $strLen ; $i++ )    {
	    if ( $i == $strLen )    
	        $thisIsMBChar = ! $beforeIsMBChar;
	    else    {
	        $ch = mb_substr($str, $i , 1);
	        $thisIsMBChar = ( ord($ch) > 127 );
	    }       // Endo of else
	    if (         ( $thisIsMBChar != $beforeIsMBChar ) 
	            &&   ( $substring != '' )    )    {
	        if ( $isSeparateLine && ( ! $isFirstLine ) )
	            $encodedString .= "\t";
	        if( $thisIsMBChar )      $encodedString .= $substring;
	        else      {
	            $jisSeq = mb_convert_encoding( $substring, 'ISO-2022-JP' );
	            $jisSeq .= chr(27) . '(B';
	            $bEncoded = base64_encode( $jisSeq );
	            $encodedString .= "=?ISO-2022-JP?B?$bEncoded?=";
	        }       // Endo of else
	        if ( $isSeparateLine )    $encodedString .= "\n";
	        $substring = '';
	        $isFirstLine = False;
	    }       // Endo of if
	    $substring .= $ch;
	    $beforeIsMBChar = $thisIsMBChar;
	}       // Endo of for
	return $encodedString;
}    // End of function header_base64_encode

}    // Endo of class OME

# history
# 2003/7/23 「メール送信システムの作り方大全」のサンプルとして制作
# 2003/9/13 OMEのフリーメール用に少しバージョンアップ
# 2004/3/26 クラス化した。OMEとして公開する事にした。
# 2004/4/18 バグフィックス
# 2004/4/27 バグフィックス（BccやCcができなかったのを修正）
?>
