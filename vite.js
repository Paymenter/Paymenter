const { createServer, build } = require("vite");
const path = require("path");

if (process.argv[2] === "dev") {
  async function dev() {
    const server = await createServer({
      configFile: process.argv[3]
        ? path.join("themes", process.argv[3], "vite.config.js")
        : path.normalize("themes/default/vite.config.js"),
    })
    await server.listen();
  }

  dev();
} else {
  build({
    configFile: process.argv[2]
      ? path.join("themes", process.argv[2], "vite.config.js")
      : path.normalize("themes/default/vite.config.js"),
  });
}

