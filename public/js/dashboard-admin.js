let currentPage = 1;

async function fetchUserData() {
    const response = await fetch('/api/user', {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const data = await response.json();

    if (response.ok) {
        if (!data.role || data.role.name !== 'Administrador') {
            alert('Intruso detectado. Tu nivel de acceso no es de Administrador.');
            window.location.href = '/dashboard';
            return;
        }

        document.getElementById('welcomeMessage').innerText = `Administración: ${data.name}`;
    } else {
        alert("Acceso denegado: " + (data.message || "No se pudieron obtener los datos."));
        window.location.href = '/login';
    }
}

async function fetchHistoricalData(page = 1) {
    document.getElementById('auditTableBody').innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align: center; color: #94a3b8;">Cargando...</td></tr>';
    
    const response = await fetch(`/api/admin/historical-data?page=${page}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    });

    const json = await response.json();
    const tbody = document.getElementById('auditTableBody');

    if (response.ok && json.success) {
        tbody.innerHTML = ''; 
        
        const records = json.data.data;
        const meta = json.data;
        
        if(records.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align:center; color: #64748b;">No hay registros.</td></tr>';
            document.getElementById('paginationInfo').innerText = '';
            return;
        }

        records.forEach(log => {
            const date = new Date(log.created_at).toLocaleString('es-ES');
            const userName = log.user ? `${log.user.name} <br><span style="color:#64748b;font-size:11px;">${log.user.email}</span>` : 'Desconocido';
            
            let statusBadge = '';
            const lowerStatus = log.status ? log.status.toLowerCase() : '';
            if(lowerStatus.includes('success')) {
                statusBadge = '<span style="background: #f0fdf4; color: #166534; padding: 4px 8px; border-radius: 4px; font-weight: 500; font-size: 11px; border: 1px solid #bbf7d0;">Exitoso</span>';
            } else if(lowerStatus.includes('failed')) {
                statusBadge = '<span style="background: #fef2f2; color: #991b1b; padding: 4px 8px; border-radius: 4px; font-weight: 500; font-size: 11px; border: 1px solid #fecaca;">Fallido</span>';
            } else if(lowerStatus.includes('locked')) {
                statusBadge = '<span style="background: #fefce8; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-weight: 500; font-size: 11px; border: 1px solid #fef08a;">Bloqueado</span>';
            } else {
                statusBadge = `<span style="background: #f1f5f9; color: #475569; padding: 4px 8px; border-radius: 4px; font-weight: 500; font-size: 11px; border: 1px solid #cbd5e1;">${log.status}</span>`;
            }

            const tr = document.createElement('tr');
            tr.style.borderBottom = "1px solid #e2e8f0";
            tr.innerHTML = `
                <td style="padding: 12px 15px; color: #475569;">${date}</td>
                <td style="padding: 12px 15px; color: #0f172a; font-weight: 500;">${userName}</td>
                <td style="padding: 12px 15px; font-family: ui-monospace, SFMono-Regular, monospace; color: #475569;">${log.ip_address}</td>
                <td style="padding: 12px 15px;">${statusBadge}</td>
            `;
            tbody.appendChild(tr);
        });

        document.getElementById('paginationInfo').innerText = `Página ${meta.current_page} de ${meta.last_page} (Total: ${meta.total})`;
        
        const btnPrev = document.getElementById('btnPrevPage');
        const btnNext = document.getElementById('btnNextPage');
        
        btnPrev.disabled = meta.current_page === 1;
        btnNext.disabled = meta.current_page === meta.last_page;

        const newBtnPrev = btnPrev.cloneNode(true);
        const newBtnNext = btnNext.cloneNode(true);
        btnPrev.parentNode.replaceChild(newBtnPrev, btnPrev);
        btnNext.parentNode.replaceChild(newBtnNext, btnNext);

        newBtnPrev.addEventListener('click', () => {
            if (meta.current_page > 1) fetchHistoricalData(meta.current_page - 1);
        });
        
        newBtnNext.addEventListener('click', () => {
            if (meta.current_page < meta.last_page) fetchHistoricalData(meta.current_page + 1);
        });

    } else {
        tbody.innerHTML = '<tr><td colspan="4" style="padding: 20px; text-align:center; color:#ef4444;">Error cargando datos.</td></tr>';
    }
}

fetchUserData();
fetchHistoricalData();
