async function fetchUserData() {
    const response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (response.ok) {
        document.getElementById('welcomeMessage').innerText = `Invitado: ${data.name}`;
        document.getElementById('apiData').innerText = `Rol Asignado: Invitado\nCorreo: ${data.email}\nNivel de Seguridad: Básico (1 Factor)`;
    } else {
        alert("Acceso denegado: " + data.message);
        window.location.href = '/login';
    }
}

fetchUserData();
