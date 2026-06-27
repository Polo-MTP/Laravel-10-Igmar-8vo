const registerForm = document.getElementById('registerForm');
const registerBtn = document.getElementById('registerBtn');
const alertBox = document.getElementById('alertBox');

const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const passwordConfirmationInput = document.getElementById('password_confirmation');

const passwordRules = document.getElementById('passwordRules');
const strengthProgress = document.getElementById('strengthProgress');
const strengthText = document.getElementById('strengthText');
const ruleLength = document.getElementById('rule-length');
const ruleLowercase = document.getElementById('rule-lowercase');
const ruleUppercase = document.getElementById('rule-uppercase');
const ruleNumber = document.getElementById('rule-number');
const ruleSpecial = document.getElementById('rule-special');

function showAlert(message, type) {
    alertBox.innerHTML = message; 
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
    if (input) input.classList.add('has-error');
    if (errorLabel) {
        errorLabel.textContent = message;
        errorLabel.style.display = 'block';
    }
}

// Setup show/hide password buttons
function setupVisibilityToggle(btnId, inputId) {
    const btn = document.getElementById(btnId);
    const input = document.getElementById(inputId);
    if (!btn || !input) return;
    btn.addEventListener('click', () => {
        const isPassword = input.type === 'password';
        input.type = isPassword ? 'text' : 'password';
        const eyeIcon = btn.querySelector('.icon-eye');
        const eyeOffIcon = btn.querySelector('.icon-eye-off');
        if (isPassword) {
            eyeIcon.classList.add('hidden');
            eyeOffIcon.classList.remove('hidden');
            btn.setAttribute('title', 'Ocultar contraseña');
            btn.setAttribute('aria-label', 'Ocultar contraseña');
        } else {
            eyeIcon.classList.remove('hidden');
            eyeOffIcon.classList.add('hidden');
            btn.setAttribute('title', 'Mostrar contraseña');
            btn.setAttribute('aria-label', 'Mostrar contraseña');
        }
    });
}

setupVisibilityToggle('togglePassword', 'password');
setupVisibilityToggle('togglePasswordConfirmation', 'password_confirmation');

// Reactive validation of password rules
function validatePasswordRules() {
    const value = passwordInput.value;
    
    if (value.length === 0) {
        passwordRules.classList.remove('visible');
        return false;
    }
    
    passwordRules.classList.add('visible');
    
    const isLengthMet = value.length >= 8;
    ruleLength.classList.toggle('met', isLengthMet);
    
    const isLowercaseMet = /[a-z]/.test(value);
    ruleLowercase.classList.toggle('met', isLowercaseMet);
    
    const isUppercaseMet = /[A-Z]/.test(value);
    ruleUppercase.classList.toggle('met', isUppercaseMet);
    
    const isNumberMet = /[0-9]/.test(value);
    ruleNumber.classList.toggle('met', isNumberMet);
    
    const isSpecialMet = /[!@#$%^&*()\-_=+]/.test(value);
    ruleSpecial.classList.toggle('met', isSpecialMet);
    
    const metCount = [isLengthMet, isLowercaseMet, isUppercaseMet, isNumberMet, isSpecialMet].filter(Boolean).length;
    const percent = (metCount / 5) * 100;
    
    strengthProgress.style.width = `${percent}%`;
    
    let color = '#ef4444'; // Red
    let text = 'Muy insegura';
    
    if (metCount === 2) {
        color = '#f97316'; // Orange
        text = 'Insegura';
    } else if (metCount === 3) {
        color = '#eab308'; // Yellow
        text = 'Aceptable';
    } else if (metCount === 4) {
        color = '#3b82f6'; // Blue
        text = 'Buena';
    } else if (metCount === 5) {
        color = '#10b981'; // Emerald Green
        text = 'Segura y robusta';
    }
    
    strengthProgress.style.backgroundColor = color;
    strengthText.textContent = `Seguridad: ${text}`;
    strengthText.style.color = color;
    
    return metCount === 5;
}

// Reactive validation of password confirmation
function validatePasswordConfirmation() {
    const passVal = passwordInput.value;
    const confirmVal = passwordConfirmationInput.value;
    const errorLabel = document.getElementById('passwordConfirmationError');
    
    if (!errorLabel) return true;
    
    if (confirmVal.length > 0 && passVal !== confirmVal) {
        passwordConfirmationInput.classList.add('has-error');
        errorLabel.textContent = 'Las contraseñas no coinciden.';
        errorLabel.style.display = 'block';
        return false;
    } else {
        passwordConfirmationInput.classList.remove('has-error');
        errorLabel.style.display = 'none';
        return true;
    }
}

// Real-time event listeners
passwordInput.addEventListener('input', () => {
    validatePasswordRules();
    validatePasswordConfirmation();
});

passwordConfirmationInput.addEventListener('input', validatePasswordConfirmation);

// Handle form submission
registerForm.addEventListener('submit', async (e) => {
    e.preventDefault(); 
    clearErrors();

    // Client-side validations
    let hasErrors = false;

    if (!nameInput.value.trim()) {
        showInlineError('name', 'nameError', 'El nombre es obligatorio.');
        hasErrors = true;
    }

    if (!emailInput.value.trim()) {
        showInlineError('email', 'emailError', 'El correo electrónico es obligatorio.');
        hasErrors = true;
    }

    const isPasswordSecure = validatePasswordRules();
    if (!passwordInput.value) {
        showInlineError('password', 'passwordError', 'La contraseña es obligatoria.');
        hasErrors = true;
    } else if (!isPasswordSecure) {
        showInlineError('password', 'passwordError', 'La contraseña no cumple con todos los requisitos de seguridad.');
        hasErrors = true;
    }

    const doesPasswordMatch = validatePasswordConfirmation();
    if (!passwordConfirmationInput.value) {
        showInlineError('password_confirmation', 'passwordConfirmationError', 'Debes confirmar la contraseña.');
        hasErrors = true;
    } else if (!doesPasswordMatch) {
        showInlineError('password_confirmation', 'passwordConfirmationError', 'Las contraseñas no coinciden.');
        hasErrors = true;
    }

    if (hasErrors) {
        return;
    }

    registerBtn.textContent = 'Creando cuenta...';
    registerBtn.disabled = true;

    grecaptcha.ready(function() {
        grecaptcha.execute(recaptchaSiteKey, {action: 'register'}).then(async function(token) {
            
            const payload = {
                name: nameInput.value,
                email: emailInput.value,
                password: passwordInput.value,
                password_confirmation: passwordConfirmationInput.value,
                recaptcha: token
            };

            try {
                const response = await fetch('/api/register', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });
                
                const data = await response.json();

                if (response.ok || response.status === 201) {
                    showAlert('¡Cuenta creada exitosamente! Redirigiendo al inicio de sesión...', 'success');
                    registerForm.reset();
                    passwordRules.classList.remove('visible');
                    
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 2000);
                } else {
                    if (data.errors) {
                        if (data.errors.name) showInlineError('name', 'nameError', data.errors.name[0]);
                        if (data.errors.email) showInlineError('email', 'emailError', data.errors.email[0]);
                        if (data.errors.password) showInlineError('password', 'passwordError', data.errors.password[0]);
                        if (data.errors.recaptcha) showAlert(data.errors.recaptcha[0], 'error');
                    } else {
                        showAlert(data.message || 'Error al registrar.', 'error');
                    }
                }
            } catch (error) {
                showAlert('Error de conexión con el servidor. Inténtalo de nuevo.', 'error');
            } finally {
                registerBtn.textContent = 'Crear Cuenta';
                registerBtn.disabled = false;
            }
        });
    });
});
