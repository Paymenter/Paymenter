Vagrant.configure("2") do |config|
  config.vm.box = "almalinux/9"
  config.vm.network "forwarded_port", guest: 3000, host: 3000, host_ip: "0.0.0.0"
  config.vm.provider "virtualbox" do |vb|
      vb.memory = "8192"
      vb.cpus = "4"
  end
  
  # setup the synced folder and provision the VM
  config.vm.synced_folder ".", "/var/www/paymenter"
  config.vm.provision "shell", path: "vagrant/provision.sh"
  config.vm.post_up_message = "Paymenter is up and running at http://localhost:3000. Login with username: dev@pyro.host, password: 'password'."
end
