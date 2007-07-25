# INIファイル
#
# @package jp.co.b-shock.carrot
# @author 小石達也 <tkoishi@b-shock.co.jp>
# @version $Id: IniFile.rb 365 2007-07-24 15:55:31Z pooza $

class IniFile < File
  attr_accessor :prefix

  def readlines
    if @lines == nil
      @lines = []
      File.open(self.path) do |file|
        file.readlines.each do |line|
          line.chomp!.gsub!(/;.*$/, '')
          @lines.push(line) if (/^ *$/ != line)
        end
      end
    end
    return @lines
  end

  def settings
    if @settings == nil
      @settings = {}
      section_name = nil
      readlines.each do |line|
        if /^\[\..*\] *$/ =~ line
          section_name = nil
        elsif /^\[(.*)\] *$/ =~ line
          section_name = $~.captures[0]
        elsif /^ *([a-z0-9_]+) *= *[\'\"]?([^\'\"]*)[\'\"]? *$/i =~ line
          key = $~.captures
          value = key.pop
          key.unshift(section_name) if (section_name != nil)
          key.unshift(self.prefix) if (self.prefix != nil)
          @settings.store(key.join('_').upcase, value)
        end
      end
    end
    return @settings
  end
end

ROOT_DIR = File.dirname(File.dirname(File.dirname(File.expand_path(__FILE__))))
ini = IniFile.new(ROOT_DIR + '/webapp/config/constant/carrot.ini')
ini.prefix = 'bs'
p ini.settings
