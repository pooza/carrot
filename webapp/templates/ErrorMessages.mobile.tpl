{*
エラーメッセージ表示 テンプレート

@package org.carrot-framework
@author 小石達也 <tkoishi@b-shock.co.jp>

*}
{if $errors}
<p>
	<font size="2" color="red">
	{foreach from=$errors key=code item=message}
		{if !$hide_error_code}{$code|translate:$error_code_dictionary}:{/if}
		{$message|url2link|nl2br}<br />
	{/foreach}
	</font>
</p>
{/if}

{* vim: set tabstop=4: *}