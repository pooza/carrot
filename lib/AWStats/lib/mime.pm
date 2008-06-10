# AWSTATS MIME DATABASE
#-------------------------------------------------------
# If you want to add MIME types,
# you must add an entry in MimeFamily and may be MimeHashLib
#-------------------------------------------------------
# $Revision: 1.23 $ - $Author: eldy $ - $Date: 2006/02/14 21:31:15 $


#package AWSMIME;


# MimeHashLib
# List of mime's label ("mime id in lower case", "mime text")
#---------------------------------------------------------------
%MimeHashLib = (
'text',      'テキストファイル',
'page',      '静的なHTMLもしくはXMLファイル',
'script',    '動的なHTMLページもしくはスクリプトファイル',
'pl',        'Perlスクリプトファイル',
'php',       'PHPスクリプトファイル',
'image',     'イメージファイル',
'document',  '文書ファイル',
'package',   'パッケージファイル',
'archive',   'アーカイブファイル',
'audio',     'オーディオクリップ',
'video',     'ビデオクリップ',
'javascript','Javaスクリプトファイル',
'vbs',       'Visual Basicスクリプトファイル',
'conf',      'Configファイル',
'css',       'CSS(Cascading Style Sheet)ファイル',
'xsl',       'XSL(Extensible Stylesheet Language)ファイル',
'runtime',   '動的なHTMLもしくはバイナリのランタイム',
'library',   'バイナリのライブラリ',
'swf',       'Macromedia Flashアニメーション',
'dtd',       'DTD(Document Type Definition)ファイル',
'csv',       'CSV(Comma Separated Value)ファイル',
'jnlp',      'Java Web Start launch file',
'lit',       'Microsoft Reader e-book',
'en',        '英語HTMLもしくはXMLファイル',
'ja',        '日本語HTMLもしくはXMLファイル',
'sjis',      '日本語HTMLもしくはXMLファイル(Shift JIS)',
'utf8',      'HTMLもしくはXMLファイル(UTF-8)',
'iso8859-1', 'HTMLもしくはXMLファイル(iso8859-1)',
'eot',       'Web埋め込みフォント',
'lit',       'Microsoft Reader e-book',
'svg',       'Scalable Vector Graphics',
'ai',        'Adobe Illustratorファイル',
'phshop',    'Adobe Photoshopイメージファイル',
'ttf',       'TrueTypeフォントファイル',
'fon',       'フォントファイル',
'pdf',       'Adobe Acrobatファイル',
'rdf',       'Resource Definition Framework',
);

# MimeHashIcon
# Each Mime ID is associated to a string that is the name of icon
# file for this Mime type.
#---------------------------------------------------------------------------
%MimeHashIcon = (
# Text file
'txt','text',
'log','text',
# HTML Static page
'html','html',
'htm','html',
'hdml','html',
'wml','html',
'wmlp','html',
'xhtml','html',
'xml','html',
'vak','glasses',
'sgm','html',
'sgml','html',
'ja','html',
'en','html',
'sjis','html',
'utf8','html',
'iso8859-1','html',
# HTML Dynamic pages or script
'asp','script',
'aspx','script',
'asmx','script',
'cfm','script',
'jsp','script',
'cgi','script',
'ksh','script',
'php','php',
'php3','php',
'php4','php',
'pl','pl',
'py','script',
'sh','script',
'shtml','html',
'tcl','script',
'xsp','script',
# Image
'gif','image',
'png','image',
'bmp','image',
'jpg','image',
'jpeg','image',
'cdr','image',
'ico','image',
'svg','svg',
'psd','phshop',
# Document
'doc','doc',
'wmz','doc',
'rtf','doc',
'pdf','pdf',
'xls','xls',
'ppt','ppt',
'pps','ppt',
'sxw','other',
'sxc','other',
'sxi','other',
'sxd','other',
'csv','other',
'xsl','html',
'lit','lit',
'ai','ai',
'rdf','rss',
# Package
'rpm',($LogType eq 'S'?'audio':'archive'),
'deb','archive',
'msi','archive',
'dmg','archive',
# Archive
'7z','archive',
'ace','archive',
'bz2','archive',
'gz','archive',
'jar','archive',
'rar','archive',
'tar','archive',
'tgz','archive',
'tbz2','archive',
'z','archive',
'zip','archive',
# Audio
'mp3','audio',
'ogg','audio',
'wma','audio',
'wav','audio',
# Video
'avi','video',
'divx','video',
'mp4','video',
'mpeg','video',
'mpg','video',
'rm','real',
'swf','flash',
'wmv','video',
'mov','quicktime',
'qt','quicktime',
# Web scripts
'js','jscript',
'vbs','jscript',
# Config
'cf','other',
'conf','other',
'css','other',
'ini','other',
'dtd','other',
# Program
'exe','script',
'dll','script',
'jnlp','jnlp',
# Fonts
'ttf','ttf',
'fon','fon',
'eot','eot',
);


%MimeHashFamily=(
# Text file
'txt','text',
'log','text',
# HTML Static page
'html','page',
'htm','page',
'wml','page',
'wmlp','page',
'xhtml','page',
'xml','page',
'vak','page',
'sgm','page',
'sgml','page',
'ja','ja',
'en','en',
'sjis','sjis',
'utf8','utf8',
'iso8859-1','iso8859-1',
# HTML Dynamic pages or script
'asp','script',
'aspx','script',
'asmx','script',
'cfm','script',
'jsp','script',
'cgi','script',
'ksh','script',
'php','php',
'php3','php',
'php4','php',
'pl','pl',
'py','script',
'sh','script',
'shtml','script',
'tcl','script',
'xsp','script',
# Image
'gif','image',
'png','image',
'bmp','image',
'jpg','image',
'jpeg','image',
'cdr','image',
'ico','image',
'svg','svg',
'psd','phshop',
'ai','ai',
# Document
'doc','document',
'wmz','document',
'rtf','document',
'pdf','pdf',
'xls','document',
'ppt','document',
'pps','document',
'sxw','document',
'sxc','document',
'sxi','document',
'sxd','document',
'csv','csv',
'xsl','xsl',
'lit','lit',
'rdf','rdf',
# Package
'rpm',($LogType eq 'S'?'audio':'package'),
'deb','package',
'msi','package',
# Archive
'7z','archive',
'ace','archive',
'bz2','archive',
'gz','archive',
'jar','archive',
'rar','archive',
'tar','archive',
'tgz','archive',
'tbz2','archive',
'z','archive',
'zip','archive',
'dmg','archive',
# Audio
'mp3','audio',
'ogg','audio',
'wav','audio',
'wma','audio',
# Video
'avi','video',
'divx','video',
'mp4','video',
'mpeg','video',
'mpg','video',
'rm','video',
'swf','swf',
'wmv','video',
'mov','video',
'qt','video',
# Web scripts
'js','javascript',
'vbs','vbs',
# Config
'cf','conf',
'conf','conf',
'css','css',
'ini','conf',
'dtd','dtd',
# Program
'exe','runtime',
'jnlp','jnlp',
'dll','library',
# Font
'ttf','ttf',
'fon','fon',
'eot','eot',
);


1;
