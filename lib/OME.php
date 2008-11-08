<?php
###########OME.php/The character set of this file is EUC-JP################
#
#  OME( Open Mail Envrionment ) for PHP   http://mac-ome.jp
#  by Masayuki Nii ( msyk@msyk.net )
#
#  ���Υ��饹�ϡ����ܸ�Ǥ��������᡼��������Ԥ�����˺�ä���ΤǤ���

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

/*	���顼��å�������������롣���顼��å�������EUC�����ܸ졣
	���Υ��饹��¿���δؿ��ϡ�����ͤ�boolean�ȤʤäƤ��ơ�������Ȥ˥��顼���ɤ�����Ƚ�̤��롣
	false�Ǥ����硢���δؿ���Ȥäƥ��顼��å�����������Ǥ��롣
*/
function getErrorMessage()	{
	return $this->errorMessage;
}

/*	�᡼�����ʸ�����ꤹ�롣��¸����ʸ���֤��������롣
*/
function setBody($str)	{
	$this->body = $str;	return true;
}

/*	�᡼�����ʸ���ɲä��롣��¸����ʸ�θ���ɲä���
*/
function appendBody($str)	{
	$this->body .= $str;	return true;
}

/*	�᡼��η�̾�����ꤹ�롣
*/
function setSubject($str)	{
	$this->subject = $str;	return true;
}

/*	�ɲäΥإå������ꤹ�롣

	ñ���ɲä���Τǡ�����Ū�ˤ�From���ϴޤޤ�ʤ��Ȥ��롣
	ʣ���Υإå��ϡ�\n�Ƕ��ڤä��ɲä��롣
	���󥳡��ǥ������ϹԤ鷺�����Τޤ���������Τǡ����ܸ��ޤ����ʤ�
	�Ǥϡ���ʬ�ǥ��󥳡��ɤ򤹤�ɬ�פ����롣
*/
function setExtraHeader($field, $value)	{
	$this->extHeaders = "$fields: $value\n";	return true;
}

/*	�᡼�륢�ɥ쥹���������������ɤ�����Ƚ�Ǥ���	*/
function checkEmail($address)	{
	if( ! eregi ("^([a-z0-9_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]+$", $address) )	{
		$this->errorMessage = "���ɥ쥹��#address�ɤ��������ʤ��᡼�륢�ɥ쥹�Ǥ���";
		return false;
	} else {
		return true;
	}
}

/*	From�ե�����ɤ����ꤹ�롣
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
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

/*	To�ե�����ɤ����ꤹ�롣���Ǥ����ꤵ��Ƥ���о�񤭤��졢���ΰ���������������Ĥ�
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹�����ꤵ��ʤ��ˡ������Ǥʤ����true
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

/*	To�ե�����ɤ��ɲä��롣
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹���ɲä���ʤ��ˡ������Ǥʤ����true
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

/*	Cc�ե�����ɤ����ꤹ�롣���Ǥ����ꤵ��Ƥ���о�񤭤��졢���ΰ���������������Ĥ�
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹�����ꤵ��ʤ��ˡ������Ǥʤ����true
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

/*	Cc�ե�����ɤ��ɲä��롣
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹���ɲä���ʤ��ˡ������Ǥʤ����true
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

/*	Bcc�ե�����ɤ����ꤹ�롣���Ǥ����ꤵ��Ƥ���о�񤭤��졢���ΰ���������������Ĥ�
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹�����ꤵ��ʤ��ˡ������Ǥʤ����true
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

/*	Bcc�ե�����ɤ��ɲä��롣
	$address: �����ԤΥ��ɥ쥹
	$name: ������̾�����ܸ�ʤɤ�EUC-JP�Ǥ���Ф��Τޤ޻����ǽ��
	�����: �᡼�륢�ɥ쥹��Ĵ�٤������ʤ�false�ʥ��ɥ쥹���ɲä���ʤ��ˡ������Ǥʤ����true
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

/*	���ꤷ���ե������ƥ�ץ졼�ȤȤ����ɤ߹��ࡣ
	���Ȥ��С�Ʊ��Υǥ��쥯�ȥ�ˤ���ե�����ʤ顢�ե�����̾�����򵭽Ҥ���Ф褤��
	���顼�װ����ե����뤬�ʤ�
*/
function setTemplateAsFile($tfile)	{
	$fileContensArray = file( $tfile );
	if ( $fileContensArray )	{
		$this->tmpContents = implode ( '', $fileContensArray );
		return true;
	}
	$this->errorMessage = "�ƥ�ץ졼�ȥե����뤬¸�ߤ��ޤ���";
	return false;
}

/*	ʸ���󤽤Τ�Τ�ƥ�ץ졼�Ȥ������ꤹ�롣
*/
function setTemplateAsString($str)	{
	$this->tmpContents = $str;
	return true;
}

/*	�ƥ�ץ졼�Ȥ˰�������������Ƥ򺹤����ߡ��������ʸ�Ȥ��롣��¸����ʸ�Ͼ�񤭤���롣
	�ƥ�ץ졼����Ρ�@@1@@�פ���$ar[0]��ʸ������֤�����롣
	�ƥ�ץ졼����Ρ�@@2@@�פ���$ar[1]��ʸ������֤�����롣�Ȥ��ä������ִ����롣
	���Ȥ��С���������Ǥ�5�ξ�硢��@@6@@�פ��@@7@@�פʤɤ��ƥ�ץ졼����˻Ĥ뤬��
	�����Ϻ������ߤ򤷤Ƥ��鶯��Ū�˺������롣������������ä����ˤ�false���᤹����
	����Ǥ⺹�����߼��ΤϹԤ��Ƥ��롣
*/
function insertToTemplate($ar)	{
	$counter = 1;
	foreach ( $ar as $aItem )	{
		$this->tmpContents = str_replace( "@@$counter@@", $aItem, $this->tmpContents );
		$counter += 1;
	}
	if ( ! ereg( '@@[0-9]*@@', $tmpContents ) )	{
		$this->body = ereg_replace('@@[0-9]*@@', '', $this->tmpContents);
		$this->errorMessage = '�������ߥƥ�ץ졼�Ȥ�;ʬ���֤�����ʸ�����@@����@@�ˤ�����ޤ�������������ޤ�����';
		return false;
	}
	$this->body = $this->tmpContents;
	return true;
}

var $bodyWidth = 74;

/*	��ʸ�μ�ư���ԤΥХ��ȿ������ꤹ�롣����ͤ�74�ˤʤäƤ��롣0����ꤹ��ȼ�ư���Ԥ��ʤ���	*/
function setBodyWidth($bytes)	{
	$this->bodyWidth = $bytes;
	return true;
}

function checkControlCodeNothing( $str )	{
	return ereg('[[:cntrl:]]', $str);
}

/*	�᡼����������롣
	To��Cc��Bcc�Υǡ����˥���ȥ��륳���ɤ����äƤ���ȴ��ʤΤǡ����ξ��ϥ��顼�����
	�����Ϥ��ʤ���ΤȤ��롣
*/
function send()	{
	if ( $this->checkControlCodeNothing ( $this->toField ) )	{
		$this->errorMessage = '����ξ���˥���ȥ��륳���ɤ��ޤޤ�Ƥ��ޤ���';
		return false;
	}
	if ( $this->checkControlCodeNothing ( $this->ccField ) )	{
		$this->errorMessage = '����ξ���˥���ȥ��륳���ɤ��ޤޤ�Ƥ��ޤ���';
		return false;
	}
	if ( $this->checkControlCodeNothing ( $this->bccField ) )	{
		$this->errorMessage = '����ξ���˥���ȥ��륳���ɤ��ޤޤ�Ƥ��ޤ���';
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
	    case "����":    return True;
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
	    case ')':    case ']':    case '}':     case '��':    case '��':
	    case '��':   case '��':   case '��':    case '��':    case '��':
	    case '��':   case '��':   case '��':    case "��":    case '��':
	    case ':':    case ';':    case '!':     case '.':     case '?': 
	    case '��':   case '��':   case '��':    case '��':    case '��':
	    case '��':   case '��':   case '��':    case '��':    case '��':
	        return True;
	}       // Endo of switch
	return False;
}    // End of function isInhibitLineTopChar

function isInhibitLineEndChar( $str )    {
	switch( $str )    {
	    case '(':     case '[':     case '{':     case '��':    case '��':
	    case '��':    case '��':    case '��':    case '��':    case '��':
	    case '��':    case '��':    case '��':    case "��":
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
# 2003/7/23 �֥᡼�����������ƥ�κ���������פΥ���ץ�Ȥ�������
# 2003/9/13 OME�Υե꡼�᡼���Ѥ˾����С�����󥢥å�
# 2004/3/26 ���饹��������OME�Ȥ��Ƹ���������ˤ�����
# 2004/4/18 �Х��ե��å���
# 2004/4/27 �Х��ե��å�����Bcc��Cc���Ǥ��ʤ��ä��Τ�����
?>
