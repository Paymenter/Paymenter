import { createServer, build } from 'vite';
import path from 'path';

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
    configFile: path.join("themes", process.argv[2] ?? 'default', "vite.config.js")
  });
}
