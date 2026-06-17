async function fetchUserData() {
    const response = await fetch('/api/user-data', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (response.ok) {
        document.getElementById('apiData').innerText = JSON.stringify(data, null, 2);
    } else {
        alert("Acceso denegado: " + data.message);
        window.location.href = '/login';
    }
}

fetchUserData();
