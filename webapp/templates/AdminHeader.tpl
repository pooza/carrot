{*
管理画面 テンプレートひな形

@package org.carrot-framework
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{if !$useragent.is_trident}<?xml version="1.0" encoding="UTF-8" ?>{/if}
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<title>{strip}
	{if $is_debug}[TEST]{/if}
	{const name='app_name_ja'}
	{$title|default:$module.title}
{/strip}</title>
{js_cache name=$jsset}
{css_cache name=$styleset}
</head>
<body {if $body.id}id="{$body.id}"{/if} class="{if $is_debug}debug{/if}">

{if $menu}
<div id="Menu">
	<ul>
		{foreach from=$menu item=item}
			{if $item.separator}
				<li class="separator">&nbsp;</li>
			{elseif $item.href}
				<li><a href="{$item.href}" target="{$item.target|default:'_blank'}">{$item.title}</a></li>
			{else}
				<li><a href="/{$item.module}/{$item.action}">{$item.title}</a></li>
			{/if}
		{/foreach}
	</ul>
</div>
<script type="text/javascript">
{literal}
document.observe('dom:loaded', function () {
  new Elevator('Menu', {
    x: 10,
    yMin: 30,
    yMargin: 10
  });
  new PeriodicalExecuter(function () {
    new Ajax.Request('/Ping', {
      method: 'get'
    });
  }, 300);
});
{/literal}
</script>
{/if}

<div id="Contents">

<div id="Header">
{const name='app_name_ja'} {$title|default:$module.title}
</div>

{* vim: set tabstop=4: *}