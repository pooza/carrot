{*
ログイン画面テンプレート

@package __PACKAGE__
@subpackage Default
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}

{include file='AdminHeader'}

<form method="post" id="LoginForm" action="/{$module}/{$action}">
	<h1>{'app_name'|translate}</h1>

	{include file='ErrorMessages' show_error_code=false}

	<table class="LoginPane">
		<tr>
			<th>メールアドレス</th>
			<td>
				<input type="text" name="email" value="{$email}" size="24" maxlength="64" class="english" />
			</td>
		</tr>
		<tr>
			<th>パスワード</th>
			<td>
				<input type="password" name="password" size="24" maxlength="64" class="english" />
			</td>
		</tr>
		<tr>
			<td colspan="2" class="BottomCell">
				<input type="submit" value="ログイン" />
			</td>
		</tr>
	</table>
</form>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
