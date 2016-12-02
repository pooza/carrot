{*
汎用テンプレート
 
@package jp.co.b-shock.carrot
@subpackage Console
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{include file='UserHeader'}

{foreach from=$errors key=code item=message}
{$code}/{$code|translate:'carrot.Console'}:  {$message}
{/foreach}

{include file='UserFooter'}
{* vim: set tabstop=4: *}
