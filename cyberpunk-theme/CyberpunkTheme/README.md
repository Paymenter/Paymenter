# Cyberpunk Theme para Paymenter

Tema **cyberpunk** completo y 100 % configurable para [Paymenter](https://paymenter.org),
pensado para hostings en español. Incluye banner con marketing rotativo, comunidad de
usuarios, reseñas en los planes, avatares personalizados, contadores en tiempo real,
páginas propias y paletas de colores.

> Paquete creado para **Sky Ultra Plus** — el marketing por defecto ya viene en español
> y habla de servidores web, Python, JavaScript, bots de WhatsApp/Discord/Telegram y PreBots.

---

## 1. Instalación (la forma fácil)

1. Entra en tu panel: **Admin → Extensions → Available Extensions**.
2. Pulsa **Upload Extension** y sube `CyberpunkTheme.zip`.
3. Ve a la pestaña **Ready to Install** e instala *Cyberpunk Theme*.
4. Listo: el tema se copia solo, se ejecutan las migraciones y el tema queda activo.

Durante la instalación la extensión:

- copia el tema a `themes/cyberpunk`,
- copia los **assets ya compilados** a `public/cyberpunk` (no necesitas Node ni `npm`),
- crea las tablas de la comunidad,
- crea la configuración por defecto,
- activa el tema Cyberpunk.

### Instalación por terminal (alternativa)

```bash
cd /ruta/a/paymenter/extensions/Others
unzip CyberpunkTheme.zip
cd CyberpunkTheme
bash install.sh
```

### Si quieres recompilar los estilos tú mismo

```bash
cd /ruta/a/paymenter
npm install
npm run build cyberpunk
```

---

## 2. Dónde se configura todo

**Admin → Extensions → Cyberpunk Theme**

| Pestaña | Qué controla |
|---|---|
| **General** | Qué bloques se ven en el inicio, opciones de tienda, textos |
| **Apariencia** | Paletas de colores, colores manuales, efectos neón/scanlines/glitch, tipografía, imagen de fondo |
| **Banner** | Diapositivas (imagen, título, texto, botón), velocidad y frases de marketing en movimiento |
| **Marketing** | Tarjetas de servicios, ventajas y accesos rápidos |
| **Páginas** | Páginas nuevas con tu propio HTML, con enlace en la barra de navegación |
| **Comunidad** | Nombre, URL, descripción, límite de archivos, reseñas de productos, avatares |
| **Redes sociales** | Facebook, Discord, Instagram, canal y grupo de WhatsApp, Telegram, YouTube, TikTok, X, GitHub |

Botones de la parte superior:

- **Guardar cambios** — aplica todo al instante.
- **Activar tema** — pone Cyberpunk como tema de la tienda.
- **Reinstalar archivos** — vuelve a copiar tema y assets sin tocar tu configuración.
- **Reiniciar visitas** — pone el contador de visitas a cero.
- **Restablecer todo** — deja el tema como recién instalado (no borra publicaciones).

Moderación de la comunidad:

- **Admin → Extensions → Comunidad · Publicaciones**
- **Admin → Extensions → Comunidad · Comentarios**

Desde ahí puedes aprobar, ocultar, destacar, editar o borrar cualquier publicación,
comentario o respuesta (también los comentarios de los productos).

---

## 3. Qué incluye el tema

### Página de inicio
- **Banner** con varias imágenes que van cambiando y frases de marketing rotativas.
- **Cinta deslizante** con las frases de marketing.
- **Estadísticas**: clientes registrados, servidores activos, productos y órdenes.
- **Contador de tiempo activo** en tiempo real, calculado desde la factura/orden más
  antigua del sistema, por lo que **nunca se reinicia** aunque recargues o reinicies
  el servidor.
- **Contador de visitas** de hoy y total (empieza en cero al instalar el tema).
- **Tarjetas de marketing** con tus servicios, **antes** de los productos.
- **Accesos rápidos** — si no configuras ninguno, el sistema detecta las categorías
  de tu tienda automáticamente.
- **Últimas publicaciones de la comunidad**.
- **Redes sociales** (sólo aparecen las que configures).

### Tienda
- Nº total de productos por categoría y **stock disponible** sumado.
- Etiqueta **Agotado** y tachado en cruz para lo que ya no está disponible.
- Etiqueta **Más popular** en los planes con más likes y comentarios.
- Likes, comentarios y respuestas en cada plan.

### Panel del cliente
- Aviso claro de **qué factura toca pagar y de qué servidor es**
  (resuelve el servicio real detrás del número de factura — útil con Pterodactyl).
- Resumen con servicios activos, facturas pendientes, tickets y próxima renovación.
- Listados de servicios y facturas rediseñados, con estado y vencimiento visibles.

### Comunidad
- Publicaciones con **imágenes y vídeos**.
- Likes, comentarios y **respuestas a comentarios**.
- Orden por recientes / más gustadas / más comentadas.
- Cada usuario borra lo suyo; los administradores lo borran todo.

### Cuenta
- **Avatar personalizado**: el usuario sube su foto y aparece en la navegación,
  en los tickets, en la comunidad y en los comentarios.

---

## 4. Compatibilidad

- Paymenter con Filament 5 / Livewire 4.
- El CSS incluye una lista blanca de utilidades habituales de Tailwind, de forma que
  las vistas de **otras extensiones** que instales después se sigan viendo bien sin
  tener que recompilar el tema.
- Los hooks del núcleo (`hook('pages.home')`, `hook('pages.dashboard')`, `hook('head')`,
  `hook('body')`, `hook('footer')`, `navigation`, `navigation.account`…) se mantienen
  intactos, así que las extensiones que los usan siguen funcionando.
- Si desinstalas la extensión, el tema sigue funcionando: sólo se desactivan la
  comunidad, las reseñas y los avatares.

---

## 5. Desinstalar

1. **Admin → Extensions → Cyberpunk Theme** → *Restablecer todo* (opcional).
2. **Admin → Settings → Theme** → vuelve a elegir `default`.
3. Desinstala la extensión desde **Admin → Extensions** (borra sus tablas).

---

## 6. Estructura del paquete

```
CyberpunkTheme/
├── CyberpunkTheme.php          # clase principal de la extensión
├── install.sh                  # instalador por terminal
├── Admin/
│   ├── Pages/CyberpunkThemePage.php     # panel de personalización
│   └── Resources/                        # moderación de la comunidad
├── Livewire/                   # comunidad, reseñas, avatar, páginas
├── Models/                     # posts, comentarios, likes, avatares, visitas
├── Support/                    # configuración, paletas, instalador, contadores
├── database/migrations/
├── resources/views/
├── routes/web.php
├── assets/                     # CSS y JS ya compilados → public/cyberpunk
└── theme/                      # el tema → themes/cyberpunk
```

---

Hecho con ⚡ para **Sky Ultra Plus**.
