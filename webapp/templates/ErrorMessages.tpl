{*
エラーメッセージ表示 テンプレート

@package jp.co.b-shock.carrot
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id: ErrorMessages.tpl 285 2007-02-14 14:06:31Z pooza $
*}
{if $errors}
<p class="alert">
	{foreach from=$errors key=code item=message}
		{if $show_error_code}{$code|translate}:{/if}
		{$message|url2link|nl2br}<br />
	{/foreach}
</p>
{/if}

{* vim: set tabstop=4 ai filetype=html: *}