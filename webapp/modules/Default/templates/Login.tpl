{*
ログイン画面テンプレート

@package __PACKAGE__
@subpackage Default
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{assign var='styleset' value='carrot.LoginForm'}
{assign var='body.id' value='LoginFormContainer'}
{include file='AdminHeader'}

{form id="LoginForm"}
	<h1>{const name='app_name_ja'}</h1>

	{include file='ErrorMessages'}

	<table>
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
			<td colspan="2" class="bottom">
				<input type="submit" value="ログイン" />
			</td>
		</tr>
	</table>
{/form}

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
