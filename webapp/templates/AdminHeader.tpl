{*
管理画面 テンプレートひな形

@package jp.co.b-shock.carrot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<title>{'app_name'|translate} {$title}</title>
<script type="text/javascript" src="js/carrot.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/?a=StyleSheet{if $styleset}&amp;style={$styleset}{/if}" />
</head>
<body>
<div id="Header">
{foreach from=$menu item=item}
	{if $item.href}
	[<a href="{$item.href}" target="{$item.target|default:'_blank'}">{$item.title}</a>]
	{elseif !$item.action}
	[<a href="/?m={$item.module}">{$item.title}</a>]
	{else}
	[<a href="/?m={$item.module}&amp;a={$item.action}">{$item.title}</a>]
	{/if}
{/foreach}
</div>

{* vim: set tabstop=4 ai filetype=html: *}