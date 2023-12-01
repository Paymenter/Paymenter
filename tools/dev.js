const { createServer } = require("vite");
const path = require("path");

async function dev() {
  const server = await createServer({
    configFile: process.argv[2]
      ? path.join("themes", process.argv[2], "vite.config.js")
      : path.normalize("themes/default/vite.config.js"),
  })
  await server.listen();
}

dev();