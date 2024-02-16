vagrant.exe up --provider virtualbox
vagrant.exe ssh -c "sudo cp /vagrant/setup.sh /setup.sh"
vagrant.exe ssh -c "sudo chmod +x /setup.sh"
vagrant.exe ssh -c "/setup.sh"
