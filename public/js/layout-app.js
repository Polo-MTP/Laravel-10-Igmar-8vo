let lastActivityTime = Date.now();
const maxIdleTimeMs = 15 * 60 * 1000;

setInterval(() => {
    const currentTime = Date.now();
    if (currentTime - lastActivityTime >= maxIdleTimeMs) {
        alert("Por motivos de seguridad, tu sesión ha expirado tras 15 minutos de inactividad.");
        document.getElementById('logoutBtn').click();
    }
}, 30000); 

const resetIdleTime = () => {
    lastActivityTime = Date.now();
};

window.addEventListener('mousemove', resetIdleTime);
window.addEventListener('keypress', resetIdleTime);
window.addEventListener('click', resetIdleTime);
window.addEventListener('scroll', resetIdleTime);

document.getElementById('logoutBtn').addEventListener('click', async () => {
    const btn = document.getElementById('logoutBtn');
    btn.innerText = 'Saliendo...';
    btn.disabled = true;
    await fetch('/logout', {
        method: 'POST',
        credentials: 'include',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-XSRF-TOKEN': decodeURIComponent(document.cookie.split('; ').find(row => row.startsWith('XSRF-TOKEN='))?.split('=')[1] || '')
        }
    });
    window.location.href = '/login';
});
