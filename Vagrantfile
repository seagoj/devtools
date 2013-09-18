require 'rubygems'
require 'json'
require 'fileutils'

Vagrant::Config.run do |config|
    cookbook_location = 'remote'
    os = 'ubuntu'
    force_update = true
    gui = false

    case os
    when 'arch'
        config.vm.box = "arch64"
        config.vm.box_url = "http://vagrant.pouss.in/archlinux_2012-07-02.box"
    else
        config.vm.box = "precise64"
        config.vm.box_url = "http://files.vagrantup.com/precise64.box"
    end

    config.vm.boot_mode = :gui if gui
    config.vm.network :bridged
    config.vm.forward_port 80, 8080
    config.vm.forward_port 6379, 6379
    config.vm.provision :chef_solo do |chef|
        chef.json = JSON.parse(File.open('chef.json').read, :symbolize_names => true);
        chef.cookbooks_path = 'cookbooks'

        case cookbook_location
        when 'local'

        when 'remote'
            FileUtils.rm_rf(chef.cookbooks_path) if force_update
            Dir.mkdir(chef.cookbooks_path) unless Dir.exists?(chef.cookbooks_path)

            chef.json[:ingredients].each do |k,v|
                k = k.to_s
                command = "git clone "+v+" #{chef.cookbooks_path}/"
                if k.index(':')
                    command += k[0,k.index(':')]
                else
                    command += k
                end
                system(command)
                chef.add_recipe(k)
            end
        end
    end
end
