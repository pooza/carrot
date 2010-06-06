{*
絵文字パレットテンプレート
 
@package org.carrot-framework
@subpackage AdminUtility
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<title>{const name='app_name_ja'} {$title}</title>
{js_cache name=$jsset}
{css_cache name=$styleset}
</head>
<body>
	<h1>{$action.title}</h1>

	<table>

{foreach from=$pictograms item='picto'}
		<tr>
			<td width="15" align="center">
				<img src="{$picto.image.url}" width="{$picto.image.width}" height="{$picto.image.height}" alt="{$picto.image.alt}" />
			</td>
			<td width="180">
				<a href="javascript:void(putSmartTag('picto',window.opener.$('{$params.field|default:'body'}'),'{$picto.name}'))">{$picto.name}</a>
			</td>
		</tr>
{/foreach}

	</table>
</body>
</html>

{* vim: set tabstop=4: *}
