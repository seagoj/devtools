require 'json'
require 'fileutils'

Vagrant.configure("2") do |config|
    options = {
        :cookbook_location => 'local',
        :cookbooks_path    => 'cookbooks',
        :os                => 'centos-6.5-parallels',
        :force_update      => false,
        :gui               => false
    }

    config.berkshelf.enabled = true

    case options[:os]
    when 'arch'
        config.vm.box = "arch64"
        config.vm.box_url = "http://vagrant.pouss.in/archlinux_2012-07-02.box"
    when 'ubuntu-saucy-parallels'
        config.vm.box = 'precise64parallels'
        config.vm.box_url = 'http://download.parallels.com/desktop/vagrant/saucy64.box'
    when 'centos-6.5-parallels'
        config.vm.box = 'centos56parallels'
        config.vm.box_url = "http://download.parallels.com/desktop/vagrant/centos64.box"
    else
        config.vm.box = "precise64"
        config.vm.box_url = "http://files.vagrantup.com/precise64.box"
    end

    config.vm.boot_mode = :gui if options[:gui]
    config.vm.hostname = File.basename(Dir.getwd)
    config.vm.network :public_network
    config.vm.provision :chef_solo do |chef|
        chef.cookbooks_path = options[:cookbooks_path]
        chef.custom_config_path = "Vagrantfile.chef"

        case options[:cookbook_location]
        when 'remote'
            cookbook = 'cookbook-bot'
            repo = "git@github.com:seagoj/#{cookbook}"
            path = "#{chef.cookbooks_path}/#{cookbook}"
            if Dir.exists?(path)
                system("cd #{path} && git pull #{repo} master")
            else
                system("git clone #{repo} #{path}")
            end
        when 'local'
        end

        chef.add_recipe('bot')
        chef.add_recipe('mysql')
    end
end
