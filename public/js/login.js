let currentMfaMethodId = null;
let currentUserId = null;

const loginForm = document.getElementById('loginForm');
const mfaForm = document.getElementById('mfaForm');
const emailOtpForm = document.getElementById('emailOtpForm');
const alertBox = document.getElementById('alertBox');

function showAlert(message, type) {
    alertBox.textContent = message;
    alertBox.className = `alert alert-${type}`;
    alertBox.classList.remove('hidden');
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.style.display = 'none');
    document.querySelectorAll('input').forEach(el => el.classList.remove('has-error'));
    alertBox.classList.add('hidden');
}

function showInlineError(inputId, errorElementId, message) {
    const input = document.getElementById(inputId);
    const errorLabel = document.getElementById(errorElementId);
    input.classList.add('has-error');
    errorLabel.textContent = message;
    errorLabel.style.display = 'block';
}

function redirectByUserRole(user) {
    if (user && user.role) {
        if (user.role.name === 'Administrador') {
            window.location.href = '/dashboard-admin';
            return;
        } else if (user.role.name === 'Invitado') {
            window.location.href = '/dashboard-invitado';
            return;
        }
    }
    window.location.href = '/dashboard';
}

loginForm.addEventListener('submit', async (e) => {
    e.preventDefault(); 
    clearErrors();

    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');

    const btn = document.getElementById('loginBtn');
    btn.textContent = 'Verificando...';
    btn.disabled = true;

    grecaptcha.ready(function() {
        grecaptcha.execute(recaptchaSiteKey, {action: 'login'}).then(async function(token) {
                const response = await fetch('/api/login', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        email: emailInput.value,
                        password: passwordInput.value,
                        recaptcha: token
                    })
                });
                const data = await response.json();

                if (response.ok && data.success) {
                    if (data.requires_setup) {
                        showAlert('MFA no configurado. Redirigiendo...', 'success');
                        setTimeout(() => window.location.href = data.setup_url, 1000);
                    } else if (data.requires_mfa) {
                        currentMfaMethodId = data.mfa_method_id; 
                        loginForm.classList.add('hidden');
                        mfaForm.classList.remove('hidden');
                        mfaForm.classList.add('fade-in');
                        document.getElementById('mfa_code').focus();
                    } else {
                        redirectByUserRole(data.user);
                    }
                } else {
                    if (data.errors) {
                        if (data.errors.email) showInlineError('email', 'emailError', data.errors.email[0]);
                        if (data.errors.password) showInlineError('password', 'passwordError', data.errors.password[0]);
                        if (data.errors.recaptcha) showAlert(data.errors.recaptcha[0], 'error');
                    } else {
                        showAlert(data.message || 'Credenciales incorrectas.', 'error');
                    }
                }
                btn.textContent = 'Iniciar Sesión';
                btn.disabled = false;
        });
    });
});

mfaForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();
    
    const codeInput = document.getElementById('mfa_code');

    const btn = document.getElementById('mfaBtn');
    btn.textContent = 'Validando...';
    btn.disabled = true;

    const response = await fetch('/api/mfa/verify', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ mfa_method_id: currentMfaMethodId, code: codeInput.value })
    });
    const data = await response.json();

    if (response.ok && data.success) {
        if (data.requires_email_otp) {
            currentUserId = data.user_id;
            mfaForm.classList.add('hidden');
            emailOtpForm.classList.remove('hidden');
            emailOtpForm.classList.add('fade-in');
            document.getElementById('email_code').focus();
        } else {
            redirectByUserRole(data.user);
        }
    } else {
        if (data.errors && data.errors.code) {
            showInlineError('mfa_code', 'mfaError', data.errors.code[0]);
        } else {
            showInlineError('mfa_code', 'mfaError', data.message || 'Código incorrecto o expirado.');
        }
        codeInput.value = ''; 
        codeInput.focus();
    }
    btn.textContent = 'Verificar Identidad';
    btn.disabled = false;
});

emailOtpForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    clearErrors();
    
    const codeInput = document.getElementById('email_code');

    const btn = document.getElementById('emailOtpBtn');
    btn.textContent = 'Verificando...';
    btn.disabled = true;

    const response = await fetch('/api/mfa/email/verify', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ user_id: currentUserId, code: codeInput.value })
    });
    const data = await response.json();

    if (response.ok && data.success) {
        redirectByUserRole(data.user);
    } else {
        if (data.errors && data.errors.code) {
            showInlineError('email_code', 'emailOtpError', data.errors.code[0]);
        } else {
            showInlineError('email_code', 'emailOtpError', data.message || 'Código incorrecto o expirado.');
        }
        codeInput.value = ''; 
        codeInput.focus();
    }
    btn.textContent = 'Finalizar Inicio de Sesión';
    btn.disabled = false;
});
