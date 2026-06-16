const registerForm = document.getElementById('registerForm');
const registerBtn = document.getElementById('registerBtn');
const alertBox = document.getElementById('alertBox');

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
    input.classList.add('has-error');
    errorLabel.textContent = message;
    errorLabel.style.display = 'block';
}

registerForm.addEventListener('submit', async (e) => {
    e.preventDefault(); 
    clearErrors();

    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmationInput = document.getElementById('password_confirmation');

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
            registerBtn.textContent = 'Crear Cuenta';
            registerBtn.disabled = false;
        });
    });
});
