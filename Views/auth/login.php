<!DOCTYPE html>
<html lang="es">
<head>
    <title>Login - DevolutionSync</title>
    <link rel="icon" type="image/png" href="assets/img/icono.png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Sebastian Obando">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap'); 

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Montserrat', sans-serif; }

        body {
            background-image: linear-gradient(to right, #e2e2e2, #ffe5c9);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            min-height: 100vh;
            padding: 20px;
        }

        .contenedor {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.35);
            position: relative;
            overflow: hidden;
            width: 768px;
            max-width: 100%;
            min-height: 520px;
        }

        .contenedor p { font-size: 14px; line-height: 20px; letter-spacing: 0.3px; margin: 16px 0; }
        .contenedor span { font-size: 12px; }

        .contenedor button {
            background-color: #ff7b00;
            color: #fff;
            font-size: 12px;
            padding: 10px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .contenedor button:disabled {
            background-color: #cccccc;
            color: #888;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .contenedor button:not(:disabled):hover {
            background-color: #e66a00;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,123,0,0.3);
        }

        .contenedor form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
        }

        .contenedor input[type="text"],
        .contenedor input[type="password"] {
            background-color: #eee;
            border: none;
            margin: 6px 0;
            padding: 10px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }

        .alert {
            width: 100%;
            padding: 10px 14px;
            border-radius: 8px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 500;
            text-align: center;
            display: none;
        }
        .alert-error   { background-color: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }
        .alert-success { background-color: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
        .alert-warning { background-color: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }

        .recaptcha-wrapper {
            display: flex;
            justify-content: center;
            margin: 8px 0 4px 0;
            width: 100%;
            transform: scale(0.88);
            transform-origin: center;
        }

        /* ── Checkbox tratamiento de datos ── */
        .datos-wrapper {
            width: 100%;
            margin: 6px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fff8f0;
            border: 1px solid #ffddb3;
            border-radius: 8px;
            padding: 10px 12px;
        }

        .datos-wrapper input[type="checkbox"] {
            margin-top: 3px;
            width: 15px;
            height: 15px;
            min-width: 15px;
            accent-color: #ff7b00;
            cursor: pointer;
        }

        .datos-wrapper label {
            font-size: 11.5px;
            color: #555;
            line-height: 1.55;
            cursor: pointer;
        }

        .datos-wrapper label .link-datos {
            color: #ff7b00;
            font-weight: 700;
            text-decoration: underline;
            cursor: pointer;
            background: none;
            border: none;
            padding: 0;
            font-size: 11.5px;
            font-family: inherit;
        }

        .datos-wrapper label .link-datos:hover { color: #e66a00; }

        .form-container {
            position: absolute;
            top: 0;
            height: 100%;
            transition: all 0.6s ease-in-out;
        }

        .sign-in { left: 0; width: 50%; z-index: 2; }
        .contenedor.active .sign-in { transform: translateX(100%); }
        .sign-up { left: 0; width: 50%; opacity: 0; z-index: 1; }
        .contenedor.active .sign-up { transform: translateX(100%); opacity: 1; z-index: 5; animation: move 0.6s; }

        @keyframes move {
            0%, 49.99% { opacity: 0; z-index: 1; }
            50%, 100%  { opacity: 1; z-index: 5; }
        }

        .toggle-container {
            position: absolute; top: 0; left: 50%;
            width: 50%; height: 100%;
            overflow: hidden;
            transition: all 0.6s ease-in-out;
            border-radius: 150px 0 0 100px;
            z-index: 1000;
        }
        .contenedor.active .toggle-container { transform: translateX(-100%); border-radius: 0 150px 100px 0; }

        .toggle {
            background: linear-gradient(to bottom, #ff7b00, #ff9a3c);
            color: #fff;
            position: relative; left: -100%;
            height: 100%; width: 200%;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }
        .contenedor.active .toggle { transform: translateX(50%); }

        .toggle-panel {
            position: absolute; width: 50%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; padding: 0 30px;
            text-align: center; top: 0;
            transform: translateX(0);
            transition: all 0.6s ease-in-out;
        }
        .toggle-left { transform: translateX(-200%); }
        .contenedor.active .toggle-left { transform: translateX(0); }
        .toggle-right { right: 0; transform: translateX(0); }
        .contenedor.active .toggle-right { transform: translateX(200%); }

        .copyright {
            display: inline-block;
            padding: 10px 15px;
            background-color: #fafafa;
            color: black;
            border-radius: 4px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 18px;
        }

        /* ══ MODAL TRATAMIENTO DE DATOS ══ */
        .modal-datos {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-datos.show { display: flex; }

        .modal-datos-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 680px;
            max-height: 88vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 15px 50px rgba(0,0,0,0.3);
            animation: slideUpModal 0.3s ease;
        }

        @keyframes slideUpModal {
            from { opacity: 0; transform: translateY(40px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .modal-datos-header {
            background: linear-gradient(135deg, #ff7b00, #ff9a3c);
            color: white;
            padding: 20px 26px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .modal-datos-header h2 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-datos-close {
            background: rgba(255,255,255,0.2);
            color: white;
            border: none;
            width: 34px; height: 34px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
            display: flex; align-items: center; justify-content: center;
        }
        .modal-datos-close:hover { background: rgba(255,255,255,0.35); transform: rotate(90deg); }

        .modal-datos-body {
            padding: 24px 28px;
            overflow-y: auto;
            flex: 1;
            font-size: 13px;
            color: #333;
            line-height: 1.75;
        }

        .modal-datos-body h3 {
            font-size: 14px;
            font-weight: 700;
            color: #ff7b00;
            margin: 18px 0 7px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ffe0b2;
        }
        .modal-datos-body h3:first-child { margin-top: 0; }
        .modal-datos-body p { margin-bottom: 8px; }
        .modal-datos-body ul { margin: 5px 0 8px 18px; }
        .modal-datos-body ul li { margin-bottom: 3px; }

        .modal-datos-footer {
            padding: 14px 26px;
            background: #f8f9fa;
            border-radius: 0 0 16px 16px;
            display: flex;
            justify-content: flex-end;
            border-top: 1px solid #eee;
            flex-shrink: 0;
        }

        .btn-aceptar-datos {
            background: linear-gradient(135deg, #ff7b00, #ff9a3c);
            color: white;
            border: none;
            padding: 10px 26px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            letter-spacing: 0.4px;
        }
        .btn-aceptar-datos:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(255,123,0,0.35); }

        /* ── Responsive login ── */
        @media (max-width: 600px) {
            .contenedor { min-height: auto; border-radius: 16px; }
            .toggle-container { display: none; }
            .form-container.sign-in { position: relative; width: 100%; left: 0; }
            .contenedor form { padding: 28px 20px; }
            .recaptcha-wrapper { transform: scale(0.76); }
        }
    </style>
</head>
<body>
    
    <div class="contenedor" id="contenedor">
        <div class="form-container sign-in">
            <form id="loginForm">
                <h1>Iniciar Sesión</h1>
                <br>
                <span>Ingresa tus credenciales para acceder al sistema</span>

                <div id="alertMessage" class="alert"></div>

                <input type="text" id="username" name="username"
                       placeholder="Ingresa tu usuario" maxlength="10" required autocomplete="username">

                <input type="password" id="password" name="password"
                       placeholder="Ingresa tu contraseña" required autocomplete="current-password">

                <!-- reCAPTCHA -->
                <div class="recaptcha-wrapper">
                    <div class="g-recaptcha"
                         data-sitekey="6LcUafsrAAAAAIpMZzqTmXPQmM6WDRb7UQGd_6t-"
                         data-callback="onCaptchaSuccess"
                         data-expired-callback="onCaptchaExpired">
                    </div>
                </div>

                <!-- Checkbox Tratamiento de Datos -->
                <div class="datos-wrapper">
                    <input type="checkbox" id="aceptaDatos" onchange="verificarCondiciones()">
                    <label for="aceptaDatos">
                        He leído y acepto la 
                        <button type="button" class="link-datos" onclick="abrirModalDatos()">
                            Política de Tratamiento de Datos Personales
                        </button>
                        de SANMARINO, conforme a la Ley 1581 de 2012 y el Decreto 1377 de 2013.
                    </label>
                </div>

                <!-- El botón solo se activa cuando reCAPTCHA + checkbox están OK -->
                <button type="submit" id="loginButton" disabled>Iniciar Sesión</button>
            </form>
        </div>
        
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>¡Hola!</h1>
                    <p>Bienvenidos a DevolutionSync — Sistema de Gestión de Devoluciones de SANMARINO.</p>
                </div>
            </div>
        </div>
    </div>

    <div>
        <div class="copyright">&#169; DevolutionSync <?php echo date('Y'); ?> — SANMARINO</div>
    </div>

    <!-- ══ MODAL: Política de Tratamiento de Datos ══ -->
    <div class="modal-datos" id="modalDatos">
        <div class="modal-datos-content">
            <div class="modal-datos-header">
                <h2><i class="fas fa-shield-alt"></i> Política de Tratamiento de Datos Personales</h2>
                <button class="modal-datos-close" onclick="cerrarModalDatos()">✕</button>
            </div>
            <div class="modal-datos-body">

                <h3>1. Responsable del Tratamiento</h3>
                <p><strong>SANMARINO — Genética Avícola</strong> es el responsable del tratamiento de los datos personales recopilados a través del sistema <strong>DevolutionSync</strong>. En cumplimiento de la Ley Estatutaria 1581 de 2012 y el Decreto Reglamentario 1377 de 2013, nos comprometemos a garantizar la confidencialidad, integridad y disponibilidad de la información que usted nos confía.</p>

                <h3>2. Datos Personales Recopilados</h3>
                <p>A través de este sistema se recopilan y tratan los siguientes datos:</p>
                <ul>
                    <li>Nombre de usuario y credenciales de acceso.</li>
                    <li>Nombre completo del usuario del sistema.</li>
                    <li>Información de clientes: nombre, NIT, dirección y correo electrónico.</li>
                    <li>Datos de las operaciones de devolución registradas.</li>
                    <li>Registros de actividad (usuario creador, fecha y hora de cada operación).</li>
                    <li>Dirección IP registrada durante el proceso de autenticación.</li>
                </ul>

                <h3>3. Finalidad del Tratamiento</h3>
                <p>Los datos personales serán utilizados exclusivamente para:</p>
                <ul>
                    <li>Gestionar el acceso seguro al sistema DevolutionSync.</li>
                    <li>Registrar, consultar y auditar las operaciones de devolución de productos.</li>
                    <li>Enviar notificaciones sobre el estado de las solicitudes de devolución.</li>
                    <li>Garantizar la trazabilidad y el control interno de las operaciones.</li>
                    <li>Cumplir con obligaciones legales y contractuales de la empresa.</li>
                </ul>

                <h3>4. Derechos del Titular</h3>
                <p>Como titular de sus datos personales, usted tiene derecho a:</p>
                <ul>
                    <li><strong>Conocer</strong> los datos personales que SANMARINO tiene sobre usted.</li>
                    <li><strong>Actualizar y rectificar</strong> sus datos cuando sean inexactos o incompletos.</li>
                    <li><strong>Solicitar prueba</strong> de la autorización otorgada para el tratamiento.</li>
                    <li><strong>Ser informado</strong> sobre el uso dado a sus datos personales.</li>
                    <li><strong>Presentar quejas</strong> ante la Superintendencia de Industria y Comercio (SIC).</li>
                    <li><strong>Revocar la autorización</strong> y/o solicitar la supresión de sus datos.</li>
                </ul>

                <h3>5. Seguridad de la Información</h3>
                <p>SANMARINO implementa medidas técnicas, administrativas y físicas para proteger los datos personales contra acceso no autorizado, pérdida, alteración o divulgación. El sistema DevolutionSync cuenta con autenticación segura, control de acceso por roles y registro de actividad.</p>

                <h3>6. Marco Legal</h3>
                <p>Esta política se rige por la Constitución Política de Colombia (artículo 15), la Ley Estatutaria 1581 de 2012, el Decreto Reglamentario 1377 de 2013 y el Decreto 103 de 2015.</p>

            </div>
            <div class="modal-datos-footer">
                <button class="btn-aceptar-datos" onclick="aceptarDesdeModal()">
                    <i class="fas fa-check"></i> Entendido — Cerrar
                </button>
            </div>
        </div>
    </div>

    <script>
        let captchaResuelto = false;

        function onCaptchaSuccess(token) {
            captchaResuelto = true;
            verificarCondiciones();
        }

        function onCaptchaExpired() {
            captchaResuelto = false;
            verificarCondiciones();
            showAlert('El reCAPTCHA ha expirado. Por favor vuelve a verificar.', 'warning');
        }

        // Botón activo SOLO si reCAPTCHA resuelto + checkbox marcado
        function verificarCondiciones() {
            const aceptaDatos = document.getElementById('aceptaDatos').checked;
            document.getElementById('loginButton').disabled = !(captchaResuelto && aceptaDatos);
        }

        function abrirModalDatos() {
            document.getElementById('modalDatos').classList.add('show');
        }

        function cerrarModalDatos() {
            document.getElementById('modalDatos').classList.remove('show');
        }

        // Al hacer clic en "Entendido" desde el modal, marca el checkbox automáticamente
        function aceptarDesdeModal() {
            document.getElementById('aceptaDatos').checked = true;
            cerrarModalDatos();
            verificarCondiciones();
        }

        document.getElementById('modalDatos').addEventListener('click', function(e) {
            if (e.target === this) cerrarModalDatos();
        });

        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const loginButton     = document.getElementById('loginButton');
            const captchaResponse = grecaptcha.getResponse();

            if (!captchaResponse) {
                showAlert('Por favor completa el reCAPTCHA antes de continuar.', 'warning');
                return;
            }

            if (!document.getElementById('aceptaDatos').checked) {
                showAlert('Debes aceptar la Política de Tratamiento de Datos para continuar.', 'warning');
                return;
            }

            const formData = new FormData(this);
            formData.append('recaptcha_token', captchaResponse);

            loginButton.disabled    = true;
            loginButton.textContent = 'Verificando...';

            fetch('index.php?url=auth/login', { method: 'POST', body: formData })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Acceso concedido. Redirigiendo...', 'success');
                        setTimeout(() => { window.location.href = data.redirect; }, 1000);
                    } else {
                        showAlert(data.message || 'Credenciales incorrectas', 'error');
                        grecaptcha.reset();
                        captchaResuelto = false;
                        document.getElementById('aceptaDatos').checked = false;
                        loginButton.disabled    = true;
                        loginButton.textContent = 'Iniciar Sesión';
                        verificarCondiciones();
                    }
                })
                .catch(() => {
                    showAlert('Error técnico en el servidor', 'error');
                    grecaptcha.reset();
                    captchaResuelto = false;
                    loginButton.disabled    = true;
                    loginButton.textContent = 'Iniciar Sesión';
                    verificarCondiciones();
                });
        });

        function showAlert(message, type) {
            const el = document.getElementById('alertMessage');
            el.textContent   = message;
            el.className     = `alert alert-${type}`;
            el.style.display = 'block';
        }

        window.addEventListener('DOMContentLoaded', () => {
            const params = new URLSearchParams(window.location.search);
            if (params.get('timeout') === '1') {
                showAlert('Su sesión ha expirado por inactividad.', 'warning');
            }
        });
    </script>   
</body>
</html>
