
<?php
// Get all extensions from app/extensions
$extensions = glob(app_path() . '/Extensions/Gateways/*', GLOB_ONLYDIR);
foreach ($extensions as $extension) {
    $extensionName = basename($extension);
    $routesFile = $extension . '/routes.php';
    if (file_exists($routesFile)) {
        require $routesFile;
    }
}
