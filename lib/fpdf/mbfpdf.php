<?php
// vim:set ts=8 sw=2 sts=2:
//--------------------------------------------------------------------------
// Multi-Byte FPDF                                       version: 1.26-kzhk
//                                          $LastChangedRevision: 296 $
// ※Carrot環境に合わせて一部修正
//--------------------------------------------------------------------------
// Usage: AddMBFont(FontName,Encoding);
//
// Example:
//    Chinese:  AddMBFont(BIG5  ,'BIG5');
//    Japanese: AddMBFont(GOTHIC,'SJIS');

// 2006/4/4 tkoishi@b-shock.co.jp
global $MBCMAP;
global $EUC2SJIS;
global $KINSOKU;
global $MBTTFDEF;
//require('fpdf.php');            // Original Class
//require('font/mbttfdef.php');   // Multi-Byte TrueType Font Define
//

// FPDF Version Check
if ((float) FPDF_VERSION < 1.51) die("You need FPDF version 1.51");

// Encoding & CMap List (CMap information from Acrobat Reader Resource/CMap folder)
$MBCMAP['BIG5']   = array ('CMap'=>'ETenms-B5-H'   ,'Ordering'=>'CNS1'  ,'Supplement'=>0);
$MBCMAP['GB']     = array ('CMap'=>'GBKp-EUC-H'    ,'Ordering'=>'GB1'   ,'Supplement'=>2);
$MBCMAP['SJIS']   = array ('CMap'=>'90msp-RKSJ-H'  ,'Ordering'=>'Japan1','Supplement'=>2);
$MBCMAP['UNIJIS'] = array ('CMap'=>'UniJIS-UTF16-H','Ordering'=>'Japan1','Supplement'=>5);
$MBCMAP['EUC-JP'] = array ('CMap'=>'EUC-H'         ,'Ordering'=>'Japan1','Supplement'=>1);
// EUC-JP has *problem* of underline and not support half-pitch characters.

// if you want convert encoding to SJIS from EUC-JP, you must change $EUC2SJIS to true.
$EUC2SJIS = mb_internal_encoding();

// 
$KINSOKU = mb_convert_encoding("。、」）・？", "sjis-win", "UTF-8");

// Short Font Name ------------------------------------------------------------
// For Acrobat Reader (Windows, MacOS, Linux, Solaris etc)
DEFINE("BIG5",    'MSungStd-Light-Acro');
DEFINE("GB",      'STSongStd-Light-Acro');
DEFINE("KOZMIN",  'KozMinProVI-Regular');
DEFINE("KOZGO",   'KozGoPro-Medium');

// For Japanese Windows Only
DEFINE("GOTHIC",  'MSGothic');
DEFINE("PGOTHIC", 'MSPGothic');
DEFINE("UIGOTHIC",'MSUIGothic');
DEFINE("MINCHO",  'MSMincho');
DEFINE("PMINCHO", 'MSPMincho');

// For Japanese Mac Only
DEFINE("HIRAKAKU",'HiraKakuPro-W3');
DEFINE("HIRAMIN", 'HiraMinPro-W3');

class MBFPDF extends FPDF
{

var $hs = 100;    // Horizontal Scaling

// For Outline, Title, Sub-Title and ETC Multi-Byte Encoding
function _unicode($txt)
{
    if (mb_detect_encoding($txt) != "ASCII") {
	$txt = chr(254).chr(255).mb_convert_encoding($txt,"UTF-16","auto");
    }
    return $txt;
}

function AddCIDFont($family,$style,$name,$cw,$CMap,$registry,$ut,$up,$mbcw=false)
{
  $i=count($this->fonts)+1;
  $fontkey=strtolower($family).strtoupper($style);
  $this->fonts[$fontkey] =
        array('i'=>$i,'type'=>'Type0','name'=>$name,'up'=>$up,'ut'=>$ut,'cw'=>$cw,'CMap'=>$CMap,'registry'=>$registry, 'mbcw'=>$mbcw);
}

function AddMBFont($family='',$enc='')
{
    global $MBTTFDEF,$MBCMAP;

    $fn = FPDF_FONTPATH."/".strtolower($family).".php";
    if (file_exists($fn)){
	include_once($fn);
    }
    $gt=$MBTTFDEF;
    $gc=$MBCMAP;
    if ($enc == '' || isset($gc[$enc]) == false) {
		// 2006/4/4 tkoishi@b-shock.co.jp
        //die("AddMBFont: ERROR Encoding [$enc] Undefine.");
		throw new BSPDFException('"%s"は不明なエンコーディングです。', $enc);
		//
    }

    if (isset($gt[$family])) {
        $ut=$gt[$family]['ut'];
        $up=$gt[$family]['up'];
        $cw=$gt[$family]['cw'];
        $mbcw=array_key_exists("mbcw", $gt[$family]);
        $cm=$gc[$enc]['CMap'];
        $od=$gc[$enc]['Ordering'];
        $sp=$gc[$enc]['Supplement'];
        $registry=array('ordering'=>$od,'supplement'=>$sp);
        $this->AddCIDFont($family,''  ,"$family"           ,$cw,$cm,$registry,$ut,$up,$mbcw);
        $this->AddCIDFont($family,'B' ,"$family,Bold"      ,$cw,$cm,$registry,$ut,$up,$mbcw);
        $this->AddCIDFont($family,'I' ,"$family,Italic"    ,$cw,$cm,$registry,$ut,$up,$mbcw);
        $this->AddCIDFont($family,'BI',"$family,BoldItalic",$cw,$cm,$registry,$ut,$up,$mbcw);
    } else {
		// 2006/4/4 tkoishi@b-shock.co.jp
        //die("AddMBFont: ERROR FontName [$family] Undefine.");
		throw new BSPDFException('"%s"は不明なフォントファミリーです。', $family);
		//
    }
}

function SetFont($family,$style='',$size=0, $hs=100)
{
  parent::SetFont($family,$style,$size);

  if ($this->hs != $hs)  $this->_out(sprintf('%d Tz', $hs));
  $this->hs = $hs;
}

function SetFontSize($size, $hs='')
{
  parent::SetFontSize($size);

  if ($hs != ''){
    if ($this->hs != $hs)  $this->_out(sprintf('%d Tz', $hs));
    $this->hs = $hs;
  }
}

function GetMBCharCode($s)
{
    if (strlen($s)==0)	    return -1;
    elseif (strlen($s)==1)  return ord($s);
    else                    return ord($s[0])*256 + ord($s[1]);
}

function GetStringWidth($s)
{
  if($this->CurrentFont['type']=='Type0'){
    if ($this->CurrentFont['mbcw'])
      return $this->GetMBStringWidth2($s) * $this->hs / 100;
    else
      return $this->GetMBStringWidth($s) * $this->hs / 100;
  } else {
    return parent::GetStringWidth($s) * $this->hs / 100;
  }
}

function GetMBStringWidth($s)
{
  $l=0;
  $cw=&$this->CurrentFont['cw'];
  $japanese = ($this->CurrentFont['registry']['ordering'] == 'Japan1');
  $nb=mb_strlen($s, "sjis-win");
  $i=0;
  while($i<$nb) {
    $c0=mb_substr($s,$i,1, "sjis-win");
    $c=$this->GetMBCharCode($c0);
    if ($c < 256){
      if (array_key_exists($c0, $cw)){
        $l+=$cw[$c0];
      } elseif ($japanese){
        $l+=500;
      } else {
	$l+=1000;
      }
    } else {
      $l+=1000;
    }
    $i+=1;
  }
  return $l*$this->FontSize/1000;
}

function GetMBStringWidth2($s)
{
  $l=0;
  $cw=&$this->CurrentFont['cw'];
  $japanese = ($this->CurrentFont['registry']['ordering'] == 'Japan1');
  $nb=mb_strlen($s, "sjis-win");
  $i=0;
  while($i<$nb){
    $c=$this->GetMBCharCode(mb_substr($s,$i,1, "sjis-win"));
    if (array_key_exists($c, $cw)){
      $l+=$cw[$c][0];
    } elseif ($c < 128){
      $l+=500;
    } else {
      $l+=1000;
    }
    $i+=1;
  }
  return $l*$this->FontSize/1000;
}

// Function Cell override for Encode Change.
function Cell($w, $h = 0, $txt = '', $border = 0, $ln = 0, $align = '', $fill = 0, $link = '', $convert=true)
{
    // For Japanese Encode Change
    global $EUC2SJIS;
    if ($convert && $EUC2SJIS) {
        $txt = mb_convert_encoding($txt,"sjis-win",$EUC2SJIS);
    }

    $k = $this->k;

    if ($this->y + $h > $this->PageBreakTrigger
        && !$this->InFooter
        && $this->AcceptPageBreak()) {
        $x  = $this->x;
        $ws = $this->ws;
        if ($ws > 0) {
            $this->ws = 0;
            $this->_out('0 Tw');
        }
        $this->AddPage($this->CurOrientation);
        $this->x = $x;
        if ($ws > 0) {
            $this->ws = $ws;
            $this->_out(sprintf('%.3f Tw', $ws * $k));
        }
    } // end if

    if ($w == 0) {
        $w = $this->w - $this->rMargin - $this->x;
    }

    $s          = '';
    if ($fill == 1 || $border == 1) {
        if ($fill == 1) {
            $op = ($border == 1) ? 'B' : 'f';
        } else {
            $op = 'S';
        }
        $s      = sprintf('%.2f %.2f %.2f %.2f re %s ', $this->x * $k, ($this->h - $this->y) * $k, $w * $k, -$h * $k, $op);
    } // end if

    if (is_string($border)) {
        $x     = $this->x;
        $y     = $this->y;
        if (strpos(' ' . $border, 'L')) {
            $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, $x * $k, ($this->h - ($y+$h)) * $k);
        }
        if (strpos(' ' . $border, 'T')) {
            $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - $y) * $k);
        }
        if (strpos(' ' . $border, 'R')) {
            $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', ($x + $w) * $k, ($this->h - $y) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
        if (strpos(' ' . $border, 'B')) {
            $s .= sprintf('%.2f %.2f m %.2f %.2f l S ', $x * $k, ($this->h - ($y + $h)) * $k, ($x + $w) * $k, ($this->h - ($y + $h)) * $k);
        }
    } // end if


    if ($txt != '') {
        if ($align == 'R') {
            $dx = $w - $this->cMargin - $this->GetStringWidth($txt);
        }
        else if ($align == 'C') {
            $dx = ($w - $this->GetStringWidth($txt)) / 2;
        }
        else {
            $dx = $this->cMargin;
        }
        $txt    = str_replace(')', '\\)', str_replace('(', '\\(', str_replace('\\', '\\\\', $txt)));
        if ($this->ColorFlag) {
            $s  .= 'q ' . $this->TextColor . ' ';
        }
        $s      .= sprintf('BT %.2f %.2f Td (%s) Tj ET', ($this->x + $dx) * $k, ($this->h - ($this->y + .5 * $h + .3 * $this->FontSize)) * $k, $txt);
        $txt = stripslashes($txt);
        if ($this->underline) {
	    $s  .= ' ' . $this->_dounderline($this->x+$dx, $this->y + .5 * $h + .3 * $this->FontSize, $txt);
        }
        if ($this->ColorFlag) {
            $s  .= ' Q';
        }
        if ($link) {
            $this->Link($this->x + $dx, $this->y + .5 * $h - .5 * $this->FontSize, $this->GetStringWidth($txt), $this->FontSize, $link);
        }
    } // end if

    if ($s) {
        $this->_out($s);
    }
    $this->lasth = $h;

    if ($ln > 0) {
        // Go to next line
        $this->y     += $h;
        if ($ln == 1) {
            $this->x = $this->lMargin;
        }
    } else {
        $this->x     += $w;
    }
} // end of the "Cell()" method

function MBWrapString($s, $wmax, $j)
{
  global $KINSOKU;

  $cw=&$this->CurrentFont['cw'];
  if($this->CurrentFont['type']=='Type0'){
    $mbcw=$this->CurrentFont['mbcw'];
    $japanese = ($this->CurrentFont['registry']['ordering'] == 'Japan1');
  } else {
    $mbcw=False;
    $japanese = False;
  }
  $nb=mb_strlen($s, "sjis-win");

  $sep=-1;
  $i=$j;
  $l=0;
  $ret="";
  $sepc="";
  $nlflg = false;
  while($i < $nb){
    $cs = mb_substr($s,$i,1, "sjis-win");
    if ($mbcw)  $c = $this->GetMBCharCode($cs);
    else	$c = ord($cs);
    $ascii = ($c>0 && $c<256);

    // 改行コードなら、そこまでで１行
    if($cs == "\n") {
      $ret = mb_substr($s,$j,$i-$j, "sjis-win");
      $i += 1;
      $nlflg = true;
      break;
    }

    // １文字分の長さ追加
    $l += $this->GetStringWidth($cs);

    // １バイト文字以外か空白なら折返し位置候補
    if(!$ascii or $cs == ' '){
      $sep = $i;
      $sepc = $cs;
    }

    // 幅を超えた時
    if ($l > $wmax && mb_strpos($KINSOKU, $cs, 0, "sjis-win") === false){
      // 折返し位置候補なし（全て１バイト文字）か先頭の文字
      if ($sep == -1 or $i == $j){
        if($i == $j)  $i+=1;
	$ret = mb_substr($s,$j,$i-$j, "sjis-win");
      } else {
        $ret = mb_substr($s,$j,$sep-$j, "sjis-win");
        $i = ($sepc == ' ') ? $sep+1 : $sep;
      }
      $nlflg = true;
      break;
    }
    $i += 1;
  }
  if ($ret == "" && $i != $j)
    $ret = mb_substr($s,$j,$i-$j, "sjis-win");

  return Array($i, $ret, $nlflg);
}

function GetMultiCellHeight($w,$h,$txt)
{
  //Multi-byte version of MultiCell()
  global $EUC2SJIS, $KINSOKU;
  if ($EUC2SJIS) {
    $txt = mb_convert_encoding($txt,"sjis-win",$EUC2SJIS);
  }
  $s=str_replace("\r",'',$txt);
  $s=preg_replace("/\n$/",'',$s);
  $nb=mb_strlen($s, "sjis-win");

  // 表示幅の算出
  if($w==0)  $w=$this->w-$this->rMargin-$this->x;
  $wmax=($w-2*$this->cMargin);

  $i=0;
  $j=0;
  $nl=1;
  while($i < $nb) {
    list($i, $ret, $nlflg) = $this->MBWrapString($s, $wmax, $j);
    $nl++;
    $j = $i;
  }
  return ($nl-1) * $h;
}

function MultiCell($w,$h,$txt,$border=0,$align='L',$fill=0)
{
  $this->MBMultiCell($w,$h,$txt,$border,$align,$fill);
}

function MBMultiCell($w,$h,$txt,$border=0,$align='L',$fill=0)
{
  //Multi-byte version of MultiCell()
  global $EUC2SJIS, $KINSOKU;
  if ($EUC2SJIS) {
    $txt = mb_convert_encoding($txt,"sjis-win",$EUC2SJIS);
  }
  $s=str_replace("\r",'',$txt);
  $s=preg_replace("/\n$/",'',$s);
  $nb=mb_strlen($s, "sjis-win");

  // 表示幅の算出
  if($w==0)  $w=$this->w-$this->rMargin-$this->x;
  $wmax=($w-2*$this->cMargin);

  // ボーダーパターンの設定
  $b=0;
  if($border) {
    if($border == 1) {
      $border = 'LTRB';
      $b  = 'LRT';
      $b2 = 'LR';
    } else {
      $b2='';
      if(is_int(strpos($border,'L')))  $b2.='L';
      if(is_int(strpos($border,'R')))  $b2.='R';
      $b=is_int(strpos($border,'T')) ? $b2.'T' : $b2;
    }
  }

  $sep=-1;
  $i=0;
  $j=0;
  $nl=1;
  while($i < $nb) {
    list($i, $ret, $nlflg) = $this->MBWrapString($s, $wmax, $j);

    if ($i >= $nb)  if ($border and is_int(strpos($border,'B')))  $b.='B';

    // 均等割り表示の計算
    if ($align == 'J'){
      $l = $this->GetStringWidth($ret);
      $wc = mb_substr_count($ret, ' ', "sjis-win");
      if ($wc == 0){
	$ret .= " ";
        $this->ws = $wmax - $l - $this->GetStringWidth(' ');
        $this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
      } elseif ($l < $wmax){
        $this->ws = ($wmax - $l) / ($wc);
        $this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
      } elseif ($this->ws != 0){
	$this->ws = 0;
	$this->_out('0 Tw');
      }
    } elseif ($this->ws != 0){
      $this->ws = 0;
      $this->_out('0 Tw');
    }

    $this->Cell($w, $h, $ret, $b, 2, $align, $fill, "", false);

    $nl++;
    if($border and $nl==2)  $b=$b2;

    $j = $i;
  }
  if ($this->ws != 0){
    $this->ws = 0;
    $this->_out('0 Tw');
  }
  $this->x=$this->lMargin;
}

function Write($h,$txt,$link='')
{
  $this->MBWrite($h,$txt,$link);
}

function MBWrite($h,$txt,$link)
{
  //Multi-byte version of Write()
  global $EUC2SJIS;
  if ($EUC2SJIS) {
    $txt = mb_convert_encoding($txt,"sjis-win",$EUC2SJIS);
  }
  $s=str_replace("\r",'',$txt);
  $nb=mb_strlen($s, "sjis-win");
  $i=0;
  $j=0;
  $nl=1;

  // 現在の行に表示可能な幅の算出
  if ($this->w - $this->rMargin <= $this->x){
    // すでに右マージンに達していたら次の行へ
    $this->x = $this->lMargin;
    $this->y += $h;
    $nl++;
  }
  $w=$this->w-$this->rMargin-$this->x;
  $wmax=($w-2*$this->cMargin);

  while($i < $nb) {
    list($i, $ret, $nlflg) = $this->MBWrapString($s, $wmax, $j);

    // 最後まで達していた場合は、幅を表示文字分にする
    if ($i >= $nb)  $w = $this->GetStringWidth($ret);

    $this->Cell($w, $h, $ret, 0, ($nlflg ? 2 : 0), '', 0, $link, false);

    if(($nl==1 && $i < $nb) || $nlflg) {
      // 最初の行なら、２行目からは左端から表示させる
      $this->x = $this->lMargin;
      $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin);
    }

    $nl++;
    $j=$i;
  }
}

function _putfonts()
{
  $nf=$this->n;
  foreach($this->diffs as $diff)
  {
    //Encodings
    $this->_newobj();
    $this->_out('<</Type /Encoding /BaseEncoding /WinAnsiEncoding /Differences ['.$diff.']>>');
    $this->_out('endobj');
  }
  $mqr=get_magic_quotes_runtime();
  set_magic_quotes_runtime(0);
  foreach($this->FontFiles as $file=>$info)
  {
    //Font file embedding
    $this->_newobj();
    $this->FontFiles[$file]['n']=$this->n;
    if(defined('FPDF_FONTPATH'))
      $file=FPDF_FONTPATH.$file;
    $size=filesize($file);
    if(!$size)
      $this->Error('Font file not found');
    $this->_out('<</Length '.$size);
    if(substr($file,-2)=='.z')
      $this->_out('/Filter /FlateDecode');
    $this->_out('/Length1 '.$info['length1']);
    if(isset($info['length2']))
      $this->_out('/Length2 '.$info['length2'].' /Length3 0');
    $this->_out('>>');
    $f=fopen($file,'rb');
    $this->_putstream(fread($f,$size));
    fclose($f);
    $this->_out('endobj');
  }
  set_magic_quotes_runtime($mqr);
  foreach($this->fonts as $k=>$font)
  {
    //Font objects
    $this->_newobj();
    $this->fonts[$k]['n']=$this->n;
    $this->_out('<</Type /Font');
    if($font['type']=='Type0')
      $this->_putType0($font);
    else
    {
      $name=$font['name'];
      $this->_out('/BaseFont /'.$name);
      if($font['type']=='core')
      {
        //Standard font
        $this->_out('/Subtype /Type1');
        if($name!='Symbol' and $name!='ZapfDingbats')
          $this->_out('/Encoding /WinAnsiEncoding');
      }
      else
      {
        //Additional font
        $this->_out('/Subtype /'.$font['type']);
        $this->_out('/FirstChar 32');
        $this->_out('/LastChar 255');
        $this->_out('/Widths '.($this->n+1).' 0 R');
        $this->_out('/FontDescriptor '.($this->n+2).' 0 R');
        if($font['enc'])
        {
          if(isset($font['diff']))
            $this->_out('/Encoding '.($nf+$font['diff']).' 0 R');
          else
            $this->_out('/Encoding /WinAnsiEncoding');
        }
      }
      $this->_out('>>');
      $this->_out('endobj');
      if($font['type']!='core')
      {
        //Widths
        $this->_newobj();
        $cw=&$font['cw'];
        $s='[';
        for($i=32;$i<=255;$i++)
          $s.=$cw[chr($i)].' ';
        $this->_out($s.']');
        $this->_out('endobj');
        //Descriptor
        $this->_newobj();
        $s='<</Type /FontDescriptor /FontName /'.$name;
        foreach($font['desc'] as $k=>$v)
          $s.=' /'.$k.' '.$v;
        $file=$font['file'];
        if($file)
          $s.=' /FontFile'.($font['type']=='Type1' ? '' : '2').' '.$this->FontFiles[$file]['n'].' 0 R';
        $this->_out($s.'>>');
        $this->_out('endobj');
      }
    }
  }
}

function _putType0($font)
{
  //Type0
  $this->_out('/Subtype /Type0');
  $this->_out('/BaseFont /'.$font['name'].'-'.$font['CMap']);
  $this->_out('/Encoding /'.$font['CMap']);
  $this->_out('/DescendantFonts ['.($this->n+1).' 0 R]');
  $this->_out('>>');
  $this->_out('endobj');
  //CIDFont
  $this->_newobj();
  $this->_out('<</Type /Font');
  $this->_out('/Subtype /CIDFontType0');
  $this->_out('/BaseFont /'.$font['name']);
  $this->_out('/CIDSystemInfo <</Registry (Adobe) /Ordering ('.$font['registry']['ordering'].') /Supplement '.$font['registry']['supplement'].'>>');
  $this->_out('/FontDescriptor '.($this->n+1).' 0 R');
  $this->_out('/DW 1000');
  if ($font['mbcw']){
    $W=Array();
    $is=$ie=false;
    foreach($font['cw'] as $c => $w){
	$W[$w[1]] = $w[0];
	if ($is === false ||$is > $w[1])  $is = $w[1];
	if ($ie === false ||$ie < $w[1])  $ie = $w[1];
    }
    $pi=false;
    $ws="";
    for($i=$is;$i<=$ie;$i+=1){
      if (array_key_exists($i, $W)){
	if ($pi === false || $pi != $i -1){
	  if (!empty($ws))  $ws .= "]\n";
	  $ws .= $i.' [ ';
	}
	$ws .= $W[$i].' ';
	$pi = $i;
      }
    }
    if (!empty($ws))  $ws .= "]\n";
    $this->_out('/W ['.$ws.']');
  } else {
    $W='/W [1 [';
    foreach($font['cw'] as $w)
      $W.=$w.' ';
    $this->_out($W.']');
    if($font['registry']['ordering'] == 'Japan1')
      $this->_out(' 231 325 500 631 [500] 326 389 500');
    $this->_out(']');
  }
  $this->_out('>>');
  $this->_out('endobj');
  //Font descriptor
  $this->_newobj();
  $this->_out('<</Type /FontDescriptor');
  $this->_out('/FontName /'.$font['name']);
  $this->_out('/Flags 6');
  $this->_out('/FontBBox [0 0 1000 1000]');
  $this->_out('/ItalicAngle 0');
  $this->_out('/Ascent 1000');
  $this->_out('/Descent 0');
  $this->_out('/CapHeight 1000');
  $this->_out('/StemV 10');
  $this->_out('>>');
  $this->_out('endobj');
}
}
?>
