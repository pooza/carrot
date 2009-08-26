{*
ユーザー画面 テンプレートひな形

@package __PACKAGE__
@author 小石達也 <tkoishi@b-shock.co.jp>
@version $Id$
*}
<div id="Footer">
<a href="{const name='app_bts_url'}" target="_blank">{const name='app_name_en'}</a> {const name='app_ver'}
(Powered by {if 'package_name'|translate}{const name='package_name'} {const name='package_ver'} / {/if}
<a href="{const name='carrot_bts_url'}" target="_blank">{const name='carrot_name'}</a> {const name='carrot_ver'})
</div>
</div>
</body>
</html>

{* vim: set tabstop=4: *}