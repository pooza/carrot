require 'carrot/environment'
require 'carrot/constants'
require 'fileutils'

module Carrot
  class Deployer
    def self.clean
      if minc?
        puts "delete #{dest}"
        File.unlink(dest)
      end
    end

    def self.create
      raise 'kariyonをアンインストールしてください。' if kariyon?
      unless minc?
        puts "link #{ROOT_DIR} -> #{dest}"
        File.symlink(ROOT_DIR, dest)
      end
    end

    private
    def self.minc?
      return (File.symlink?(dest) &&
        File.exist?(File.join(dest, 'webapp/lib/MincSite.class.php'))
      )
    end

    def self.kariyon?
      return File.exist?(File.join(dest, '.kariyon'))
    end

    def self.dest
      return File.join(
        Carrot::Constants.new["BS_APP_DEPLOY_DIR"],
        Carrot::Environment.name
      )
    end
  end
end
