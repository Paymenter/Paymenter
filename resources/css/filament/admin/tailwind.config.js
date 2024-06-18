import preset from '../../../../vendor/filament/filament/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Admin/**/*.php',
        './resources/views/admin/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
}
