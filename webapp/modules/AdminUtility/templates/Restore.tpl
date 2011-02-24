{*
リストア画面テンプレート

@package org.carrot-framework
@subpackage AdminUtility
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{include file='AdminHeader'}

<div id="BreadCrumbs">
	<a href="#">{$action.title}</a>
</div>

<h1>{$action.title}</h1>

{include file='ErrorMessages'}

{if $is_restoreable}
	{form  onsubmit='' attachable=true}
		<div class="alert">現在の状態をバックアップすることを強くお勧めします。</div>
		<input type="file" name="file" value="アップロード" />
		<input type="submit" value="実行" />
	{/form}
{else}
	<div class="alert">この環境では、リストアを実行することが出来ません。<br/>
	SQLite以外のDBMSが使用されています。</div>
{/if}

{include file='AdminFooter'}

{* vim: set tabstop=4: *}
