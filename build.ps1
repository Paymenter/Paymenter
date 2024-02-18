param (
    [bool]$fullBuild = $false
)

vagrant.exe up

$full = if($fullBuild) { "--full" } else { "" }

# Define the spinner
$spinner = '|/-\'
$i = 0

# Execute the main task
$job = Start-Job -ScriptBlock {
    param($path)
    Set-Location $path

    vagrant.exe ssh -c "
        # Copy the build script from the /vagrant directory to the root directory
        sudo cp /vagrant/build.sh /build.sh || { echo 'Failed to copy build.sh'; exit  1; }

        # Make the build script executable
        sudo chmod +x /build.sh || { echo 'Failed to make build.sh executable'; exit  1; }

        # Execute the build script
        sudo /build.sh $full || { echo 'Failed to execute build.sh'; exit  1; }
    "
} -ArgumentList $pwd

Write-Host "`n Building... `n"
# Loop until the job is complete, displaying a spinner
while ($job.State -eq 'Running') {
    Write-Host -NoNewline "`r"
    Write-Host -NoNewline $spinner[$i]
    $i = ($i + 1) % $spinner.Length
    Start-Sleep -Milliseconds 100
}

# Get the job output
$output = Receive-Job $job

if ($null -ne $output) {
    Write-Host $output
} else {
    Write-Host "`n Build complete! `n"
}