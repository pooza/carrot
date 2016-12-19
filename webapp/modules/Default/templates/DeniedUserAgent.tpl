{*
対象外UserAgentテンプレート
 
@package jp.co.b-shock.carrot
@subpackage Default
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
{include file='UserHeader'}
ご利用のWebブラウザでは、このページを閲覧できません。
{if $is_debug}
<div class="alert">
  {foreach from=$useragent key='key' item='value'}
    {$key}: {$value}<br>
  {/foreach}
</div>
{/if}
{include file='UserFooter'}

{* vim: set tabstop=4: *}
