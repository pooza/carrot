{*
ユーザー画面 テンプレートひな形

@package __PACKAGE__
@author 小石達也 <tkoishi@b-shock.co.jp>
*}
<footer>
  {const name='app_name_en'} {const name='app_ver'}
  (Powered by {if 'package_name'|translate}{const name='package_name'} {const name='package_ver'} / {/if}
  {const name='carrot_name'} {const name='carrot_ver'})
</footer>
</body>
</html>

{* vim: set tabstop=4: *}