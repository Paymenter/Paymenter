const { build } = require("vite");
const path = require("path");

build({
  configFile: process.argv[2]
    ? path.join("themes", process.argv[2], "vite.config.js")
    : path.normalize("themes/default/vite.config.js"),
});
