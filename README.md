# Inversiones 3R

Formulario web estatico para solicitudes de credito con envio por correo via PHP y PHPMailer.

## Estructura

- `public/index.html`: formulario principal.
- `public/styles.css`: estilos del formulario.
- `public/procesar.php`: endpoint unico para recibir la solicitud y enviar el correo.
- `public/logo.jpg`: logo usado en la pantalla.
- `scripts/validate-public.js`: validacion rapida de referencias y archivos requeridos.
- `scripts/build-static.js`: copia los archivos publicables a `build`.

## Comandos

```bash
npm run check
npm run build
npm start
```

`npm start` sirve la carpeta `public` en `http://localhost:3000`.

## Configuracion de correo

El archivo `procesar.php` ya no guarda usuario ni contrasena SMTP dentro del codigo. Puedes configurarlo de dos formas.

Opcion recomendada en servidor: variables de entorno.

```bash
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=correo@dominio.com
SMTP_PASSWORD=clave-de-aplicacion
MAIL_FROM=correo@dominio.com
MAIL_FROM_NAME="Formulario Inversiones 3R"
MAIL_TO=destino@dominio.com
```

Opcion simple: copia `config/mail.example.php` como `config/mail.php` y coloca ahi tus datos reales. `config/mail.php` esta ignorado por Git para que no se suba al repositorio.

Para Gmail se debe usar una contrasena de aplicacion, no la contrasena normal de la cuenta.
