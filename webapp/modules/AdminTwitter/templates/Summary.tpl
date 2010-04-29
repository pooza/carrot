{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminTwitter
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{include file='AdminHeader'}

<h1>{$action.title}</h1>
<table class="Detail">

{if $account}
	{image_cache class='BSTwitterAccount' id=$account.screen_name size='icon'}<br/>
	<a href="{$account.timeline_url}" target="_blank">{$account.screen_name}</a>
	({$account.name})
	としてログイン中。
{else}
	{form module=$module.name action='Login'}
		<a href="{$oauth.url}" target="_blank">認証コードを取得</a><br/>
		<input type="text" name="verifier" />
		<input type="submit" value="ログイン" />
	{/form}
{/if}

</table>

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
