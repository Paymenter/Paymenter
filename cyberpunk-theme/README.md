# Cyberpunk Theme — paquete distribuible

Este directorio contiene el paquete completo del **tema Cyberpunk para Paymenter**
creado para *Sky Ultra Plus*. No modifica ningún archivo del núcleo de Paymenter
ni del tema `default`.

## Contenido

| Ruta | Qué es |
|---|---|
| `CyberpunkTheme/` | Código fuente de la extensión + el tema + los assets compilados |
| `CyberpunkTheme-v1.0.0.zip` | **El archivo que se sube** en Admin → Extensions → Upload Extension |

## Instalación rápida

1. Descarga `CyberpunkTheme-v1.0.0.zip`.
2. Panel de Paymenter → **Admin → Extensions → Available Extensions**.
3. **Upload Extension** → sube el ZIP.
4. Pestaña **Ready to Install** → instala *Cyberpunk Theme*.
5. En la pantalla de la extensión activa el interruptor **Enabled** y guarda.
6. Ve a **Admin → Extensions → Cyberpunk Theme** para personalizarlo todo.

Durante el paso 4 el instalador copia el tema a `themes/cyberpunk`, copia los assets
ya compilados a `public/cyberpunk`, ejecuta las migraciones, crea la configuración
por defecto (marketing en español) y deja el tema activo. No hace falta Node ni npm.

## Documentación completa

Está en [`CyberpunkTheme/README.md`](CyberpunkTheme/README.md).

## Regenerar el ZIP tras un cambio

```bash
# desde la raíz de Paymenter
npm run build cyberpunk                       # recompila los assets del tema
rm -rf cyberpunk-theme/CyberpunkTheme/theme cyberpunk-theme/CyberpunkTheme/assets
cp -r themes/cyberpunk  cyberpunk-theme/CyberpunkTheme/theme
cp -r public/cyberpunk  cyberpunk-theme/CyberpunkTheme/assets
cd cyberpunk-theme && zip -r CyberpunkTheme-v1.0.0.zip CyberpunkTheme
```
