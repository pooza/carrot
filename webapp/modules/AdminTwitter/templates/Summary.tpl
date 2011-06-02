{*
要約画面テンプレート

@package org.carrot-framework
@subpackage AdminTwitter
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{include file='AdminHeader'}

<h1>{$action.title}</h1>

{if $account}
	{form module=$module.name action='Logout'}
		{image_cache class='BSTwitterAccount' id=$account.screen_name size='icon'}<br/>
		<a href="{$account.timeline_url}" target="_blank">{$account.screen_name}</a>
		({$account.name})
		としてログイン中。<br/>
		<input type="submit" value="ログアウト" />
	{/form}
{else}
	{form module=$module.name action='Login'}
		<a href="{$oauth.url}" target="_blank">認証コードを取得</a><br/>
		{include file='ErrorMessages'}
		<input type="text" name="verifier" />
		<input type="submit" value="ログイン" />
	{/form}
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
