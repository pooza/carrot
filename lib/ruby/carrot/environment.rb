# サーバ環境
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>

class Environment
  def self.name
    return File.basename(ROOT_DIR)
  end

  def self.file_path
    return ROOT_DIR + '/webapp/config/constant/' + Environment.name + '.yaml'
  end

  def self.os
    os = `uname`.chomp
    if (os == 'Linux') && File.exist?('/usr/bin/apt-get')
      os = 'Debian'
    end
    return os
  end
end
