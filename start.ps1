vagrant.exe ssh -c "
# Check if the /provisioned file does not exist
if [ ! -f /provisioned ]; then
  # Copy the setup script from the /vagrant directory to the root directory
  sudo cp /vagrant/setup.sh /setup.sh || { echo 'Failed to copy setup.sh'; exit  1; }

  # Make the setup script executable
  sudo chmod +x /setup.sh || { echo 'Failed to make setup.sh executable'; exit  1; }

  # Execute the setup script
  /setup.sh || { echo 'Failed to execute setup.sh'; exit  1; }

  # If all commands succeed, create the /provisioned file as a flag
  touch /provisioned || { echo 'Failed to create /provisioned file'; exit  1; }
else
  echo '/provisioned file exists, skipping provisioning'
fi
"