// vite-hmr-server.js
const express = require("express");
const { createServer: createViteServer } = require("vite");
const { WebSocketServer } = require("ws");
const { createServer } = require("http");

async function createServer() {
  const app = express();

  // Crea servidor Vite en modo middleware
  const vite = await createViteServer({
    server: { middlewareMode: true },
    appType: "custom",
    hmr: {
      server: null, // Desactivamos el servidor HMR por defecto de Vite
    },
  });

  // Usa vite como middleware
  app.use(vite.middlewares);

  // Servir archivos estáticos desde el directorio del tema
  app.use(express.static("./"));

  // Crear servidor HTTP
  const httpServer = createServer(app);

  // Configurar servidor WebSocket para HMR
  const wsServer = new WebSocketServer({
    server: httpServer,
    path: "/hmr",
  });

  // Conectar el servidor WebSocket con el cliente HMR de Vite
  vite.ws = {
    on: (event, callback) => {
      if (event === "connection") {
        wsServer.on("connection", (socket) => {
          callback(socket);
        });
      }
    },
    send: (payload) => {
      wsServer.clients.forEach((client) => {
        if (client.readyState === 1) {
          // OPEN
          client.send(JSON.stringify(payload));
        }
      });
    },
  };

  // Iniciar el servidor en el puerto 3000
  httpServer.listen(3000, () => {
    console.log("Servidor HMR escuchando en http://localhost:3000");
    console.log(
      "Para desarrollo de WordPress, recuerda que debes usar tu servidor local de WordPress"
    );
  });
}

createServer();
