# Bitácora de Pruebas Manuales - Sistema de Login Seguro

Este documento contiene el registro de las pruebas manuales que realizamos para asegurar que todo el sistema de autenticación, seguridad y auditoría funcione correctamente.

---

## 1. Módulo de Registro

*   **Registro exitoso (Caso feliz)**
    *   **¿Qué se probó?:** Crear una cuenta nueva llenando todos los campos correctamente y pasando la prueba del reCAPTCHA.
    *   **Resultado:** La cuenta se creó sin problemas en la base de datos y se le asignó automáticamente el rol de **Invitado**. (✅ Pasó)

*   **Intento con correo ya registrado**
    *   **¿Qué se probó?:** Intentar registrar un usuario usando un correo electrónico que ya existe en el sistema.
    *   **Resultado:** El sistema detuvo el registro y mostró un mensaje indicando que el correo ya está en uso. (✅ Pasó)

*   **Contraseña insegura o muy sencilla**
    *   **¿Qué se probó?:** Intentar registrarse usando una contraseña muy débil (como "12345678").
    *   **Resultado:** El sistema rechazó la contraseña y mostró en pantalla los requisitos de seguridad que le faltaron. (✅ Pasó)

*   **Registro sin verificar el reCAPTCHA**
    *   **¿Qué se probó?:** Rellenar todos los datos correctamente pero enviar el formulario sin marcar la casilla de reCAPTCHA.
    *   **Resultado:** El sistema bloqueó el envío y avisó que la verificación anti-robots es obligatoria. (✅ Pasó)

---

## 2. Módulo de Inicio de Sesión

*   **Inicio de sesión exitoso (Caso feliz)**
    *   **¿Qué se probó?:** Ingresar con correo y contraseña correctos.
    *   **Resultado:** El sistema reconoció las credenciales y nos mandó a la pantalla para ingresar el código del celular (2FA). (✅ Pasó)

*   **Credenciales incorrectas**
    *   **¿Qué se probó?:** Intentar entrar con una contraseña equivocada.
    *   **Resultado:** Mostró el mensaje de "Credenciales incorrectas" y sumó un intento fallido al historial de la cuenta. (✅ Pasó)

*   **Bloqueo por intentos fallidos**
    *   **¿Qué se probó?:** Escribir a propósito la contraseña mal 5 veces seguidas.
    *   **Resultado:** Al quinto intento fallido, el sistema bloqueó la cuenta temporalmente por 15 minutos en la base de datos. (✅ Pasó)

*   **Acceso durante el bloqueo**
    *   **¿Qué se probó?:** Intentar iniciar sesión cuando la cuenta todavía está bloqueada.
    *   **Resultado:** El sistema impidió el intento de inmediato y mostró un mensaje indicando cuántos minutos quedan para poder volver a intentar. (✅ Pasó)

---

## 3. Módulo de Doble Factor (MFA / 3FA)

*   **Configuración inicial (Código QR)**
    *   **¿Qué se probó?:** Entrar por primera vez a configurar el doble factor de autenticación.
    *   **Resultado:** Se generó y mostró correctamente el código QR en pantalla junto con la clave secreta para la app del celular. (✅ Pasó)

*   **Vinculación exitosa de la app**
    *   **¿Qué se probó?:** Escanear el QR e ingresar un código temporal generado por la app del celular para activarlo.
    *   **Resultado:** Se validó el código, se activó la seguridad MFA en la cuenta y nos redirigió al login. (✅ Pasó)

*   **Código de vinculación incorrecto**
    *   **¿Qué se probó?:** Intentar activar el MFA ingresando un código que no coincide o ya expiró.
    *   **Resultado:** Mostró un mensaje de error y no activó la seguridad hasta poner un código correcto. (✅ Pasó)

*   **Inicio de sesión con 2FA correcto**
    *   **¿Qué se probó?:** Iniciar sesión y meter el código del celular correcto.
    *   **Resultado:** Si es un usuario normal, entra directo al dashboard. Si es Administrador, lo manda al paso del código por correo (3FA). (✅ Pasó)

*   **Inicio de sesión con 2FA incorrecto**
    *   **¿Qué se probó?:** Ingresar un código de celular equivocado al intentar loguearse.
    *   **Resultado:** El sistema rechazó el acceso y registró el fallo en el historial de auditoría. (✅ Pasó)

*   **Tercer factor (3FA - Solo Administrador) con código de correo correcto**
    *   **¿Qué se probó?:** Siendo Admin, ingresar el código especial enviado al correo.
    *   **Resultado:** El sistema nos dejó entrar y nos redirigió al panel de control del administrador. (✅ Pasó)

*   **Tercer factor (3FA - Solo Administrador) con código de correo incorrecto**
    *   **¿Qué se probó?:** Poner un código de correo equivocado o que ya pasó de su tiempo límite.
    *   **Resultado:** Rechazó el acceso y se mantuvo en la pantalla de verificación del correo. (✅ Pasó)

---

## 4. Módulo de Control de Sesiones

*   **Cuenta desactivada en tiempo real**
    *   **¿Qué se probó?:** Navegar por el sitio mientras un administrador desactiva nuestra cuenta.
    *   **Resultado:** En la siguiente acción o recarga, el sistema nos sacó automáticamente y nos mandó al login con un mensaje para contactar a soporte. (✅ Pasó)

*   **Redirección de usuario ya logueado**
    *   **¿Qué se probó?:** Intentar entrar a la página de `/login` cuando ya tenemos una sesión activa.
    *   **Resultado:** El sistema detectó la sesión activa y nos redirigió de inmediato a nuestro dashboard correspondiente según el rol. (✅ Pasó)

---

## 5. Módulo de Auditoría y Logs

*   **Registro automático de accesos**
    *   **¿Qué se probó?:** Realizar intentos de inicio de sesión (tanto exitosos como fallidos) y revisar la base de datos.
    *   **Resultado:** El sistema guardó en los logs la fecha, la IP, el navegador usado, el correo del intento y el resultado de la acción. (✅ Pasó)

*   **Visualización del historial para Administradores**
    *   **¿Qué se probó?:** Entrar al panel de administrador y revisar la tabla de auditoría.
    *   **Resultado:** La tabla cargó correctamente mostrando todos los intentos de acceso ordenados de los más recientes a los más antiguos. (✅ Pasó)
