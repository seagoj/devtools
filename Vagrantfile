Vagrant.configure("2") do |config|
    config.berkshelf.enabled = true
    config.vm.box = 'centos56parallels'
    config.vm.box_url = "http://download.parallels.com/desktop/vagrant/centos64.box"
    config.vm.hostname = File.basename(Dir.getwd)
    config.vm.network :public_network

    config.vm.provision :chef_solo do |chef|
        chef.custom_config_path = "Vagrantfile.chef"
        chef.json = {
            :mysql => {
                :server_root_password   => 'BPS4Mysql',
                :server_debian_password => 'debpass',
                :server_repl_password   => 'replpass'
            }
        }
        chef.run_list = [
            'recipe[bot::default]'
        ]
    end
end
