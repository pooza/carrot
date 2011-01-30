#!/usr/bin/env rake

# carrotユーティリティタスク
#
# @package __PACKAGE__
# @author 小石達也 <tkoishi@b-shock.co.jp>

$KCODE = 'u'

namespace :local do
  task :init => ['database:init']

  namespace :database do
    task :init => []
  end
end