{*
phpDocumentor 生成画面テンプレート

@package jp.co.b-shock.carrot
@subpackage DevelopDocumentor
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id: Generate.tpl 355 2007-06-27 13:02:34Z pooza $
*}

{include file='AdminHeader'}
{include file='ErrorMessages'}

<h1>APIドキュメント生成</h1>
<form name="frm" method="post" action="/">
	<input type="hidden" name="m" value="{$module}" />
	<input type="hidden" name="a" value="{$action}" />

	<table class="Detail">
		<tr>
			<th width="120">ディレクトリ</th>
			<td width="330">
				{html_checkboxes name='directories' options=$directory_options selected=$params.directories separator='<br/>'}
			</td>
		</tr>
		<tr>
			<th width="120">形式</th>
			<td width="330">
				{html_radios name='format' options=$formats selected=$params.format separator='<br/>'}
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="submit" value="生成する" />
				<span class="alert">※ 生成には数分かかります。</span>
			</td>
		</tr>
	</table>

</form>

<p><a href="/doc" target="_blank">生成されたドキュメントを開く</a></p>

{include file='AdminFooter'}

{* vim: set tabstop=4 ai filetype=html: *}
