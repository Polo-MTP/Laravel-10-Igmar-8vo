async function loadProfile() {
    const response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const user = await response.json();

    if (response.ok) {
        document.getElementById('profileName').innerText = user.name;
        document.getElementById('profileEmail').innerText = user.email;
        
        const statusBadge = user.is_active 
            ? '<span style="background: #dcfce3; color: #15803d; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Activa</span>' 
            : '<span style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: bold;">Inactiva</span>';
        
        document.getElementById('profileStatus').innerHTML = statusBadge;
        
        const date = new Date(user.created_at);
        document.getElementById('profileCreated').innerText = date.toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' });
        
    } else {
        alert("No se pudo cargar el perfil.");
    }
}

loadProfile();
