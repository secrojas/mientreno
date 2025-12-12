# Logo y Favicon Setup

## Archivos Requeridos

Coloca el logo principal de MiEntreno en este directorio como:
- `logo.png` - Logo principal (recomendado: al menos 500x500px con fondo transparente o negro)

## Generar Favicons

Una vez que tengas el logo principal (`logo.png`), necesitas generar los siguientes archivos de favicon:

### Opción 1: Usando un servicio online (Recomendado - Más fácil)

1. Ve a [https://realfavicongenerator.net/](https://realfavicongenerator.net/)
2. Sube tu archivo `logo.png`
3. Configura las opciones (puedes usar las predeterminadas)
4. Descarga el paquete de favicons
5. Extrae los archivos en este directorio (`public/images/`)

### Opción 2: Usando ImageMagick (Línea de comandos)

Si tienes ImageMagick instalado, ejecuta estos comandos desde el directorio `public/images/`:

```bash
# Instalar ImageMagick primero (si no lo tienes)
# Windows: choco install imagemagick
# macOS: brew install imagemagick
# Linux: apt-get install imagemagick

# Generar favicon-16x16.png
magick logo.png -resize 16x16 favicon-16x16.png

# Generar favicon-32x32.png
magick logo.png -resize 32x32 favicon-32x32.png

# Generar apple-touch-icon.png (180x180)
magick logo.png -resize 180x180 apple-touch-icon.png

# Generar android-chrome-192x192.png
magick logo.png -resize 192x192 android-chrome-192x192.png

# Generar android-chrome-512x512.png
magick logo.png -resize 512x512 android-chrome-512x512.png

# Generar favicon.ico (contiene múltiples tamaños)
magick logo.png -resize 16x16 -resize 32x32 -resize 48x48 ../favicon.ico
```

### Opción 3: Usando un script de Node.js

Instala el paquete `sharp`:
```bash
npm install sharp-cli -g
```

Luego ejecuta:
```bash
sharp -i logo.png -o favicon-16x16.png resize 16 16
sharp -i logo.png -o favicon-32x32.png resize 32 32
sharp -i logo.png -o apple-touch-icon.png resize 180 180
sharp -i logo.png -o android-chrome-192x192.png resize 192 192
sharp -i logo.png -o android-chrome-512x512.png resize 512 512
```

## Archivos Generados Necesarios

Después de generar los favicons, deberías tener los siguientes archivos en `public/images/`:

```
public/images/
├── logo.png                      # Logo principal
├── favicon-16x16.png             # Favicon 16x16
├── favicon-32x32.png             # Favicon 32x32
├── apple-touch-icon.png          # Apple Touch Icon 180x180
├── android-chrome-192x192.png    # Android Chrome 192x192
└── android-chrome-512x512.png    # Android Chrome 512x512
```

Y en `public/`:
```
public/
└── site.webmanifest              # Ya está creado ✓
```

## Verificación

Para verificar que todo funciona correctamente:

1. Coloca el archivo `logo.png` en `public/images/`
2. Genera todos los favicons usando una de las opciones anteriores
3. Abre la aplicación en el navegador
4. Verifica que el logo aparece en:
   - Landing page (navbar)
   - Página de login (navbar)
   - Dashboard (sidebar)
   - Pestaña del navegador (favicon)

## Notas

- El logo debe tener fondo transparente o negro para que se vea bien en la interfaz
- El color del logo debería ser principalmente magenta/rosa (#FF3B5C) para mantener consistencia con el branding
- Todos los archivos de favicon deberían tener el mismo contenido, solo en diferentes tamaños
- El site.webmanifest ya está configurado con los colores del tema de MiEntreno
