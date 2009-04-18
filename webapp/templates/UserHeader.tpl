{*
ユーザー画面 テンプレートひな形

@package __PACKAGE__
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
<?xml version="1.0" encoding="UTF-8" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ja" lang="ja">
<head>
<title>{const name='app_name'} {$title|default:$module.title}</title>
<script type="text/javascript" src="/JavaScript{if $jsset}?jsset={$jsset}{/if}" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/StyleSheet{if $styleset}?styleset={$styleset}{/if}" />
</head>
<body {if $body.id}id="{$body.id}"{/if}>

<div id="Contents">

<div id="Header">
{const name='app_name'} {$title|default:$module.title}
</div>

{* vim: set tabstop=4: *}