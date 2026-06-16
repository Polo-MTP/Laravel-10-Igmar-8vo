const input = document.getElementById('verify_code');
const statusMsg = document.getElementById('status_msg');

console.log("MFA Setup inicializado. Method ID:", mfaMethodId);

input.addEventListener('input', async (e) => {
    input.value = input.value.replace(/\D/g, '');
    console.log("Input detectado. Longitud actual:", input.value.length);

    if (input.value.length === 6) {
        console.log("6 dígitos detectados. Bloqueando input y enviando petición...");
        input.disabled = true;
        statusMsg.style.color = '#3b82f6';
        statusMsg.textContent = 'Verificando...';

        console.log("Llamando a la API /api/mfa/setup/confirm...");
        const response = await fetch('/api/mfa/setup/confirm', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json', 
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ mfa_method_id: mfaMethodId, code: input.value })
        });
        
        console.log("Respuesta recibida HTTP:", response.status);
        const data = await response.json();
        console.log("Datos JSON:", data);

        if (response.ok && data.success) {
            input.style.borderColor = '#10b981';
            statusMsg.style.color = '#10b981';
            statusMsg.innerHTML = '¡Verificado! ✔️ Redirigiendo...';
            
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
        } else {
            input.style.borderColor = '#ef4444';
            statusMsg.style.color = '#ef4444';
            statusMsg.textContent = data.message || 'Código incorrecto';
            input.disabled = false;
            input.value = '';
            input.focus();
        }
    } else {
        input.style.borderColor = '#cbd5e1';
        statusMsg.textContent = '';
    }
});
