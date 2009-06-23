{*
絵文字パレットテンプレート
 
@package org.carrot-framework
@subpackage UserPictogram
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<title>{const name='app_name_ja'} {$title}</title>
<script type="text/javascript" src="/JavaScript{if $jsset}?jsset={$jsset}{/if}" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/StyleSheet{if $styleset}?styleset={$styleset}{/if}" />
</head>
<body>
	<h1>{$action.title}</h1>

	<table>

{foreach from=$pictograms item='pictogram'}
		<tr>
			<td width="15" align="center">{picto name=$pictogram}</td>
			<td width="180">
				<a href="javascript:void(putSmartTag('picto','{$pictogram}',window.opener.$('{$params.field|default:'body'}')))">{$pictogram}</a>
			</td>
		</tr>
{/foreach}

	</table>
</body>
</html>

{* vim: set tabstop=4: *}
