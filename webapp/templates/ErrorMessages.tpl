{*
エラーメッセージ表示 テンプレート

@package jp.co.b-shock.carrot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
{if $errors}
<p class="alert">
	{foreach from=$errors key=code item=message}
		{if !$hide_error_code}{$code|translate:$error_code_dictionary}:{/if}
		{$message|url2link|nl2br}<br />
	{/foreach}
</p>
{/if}

{* vim: set tabstop=4 ai filetype=html: *}