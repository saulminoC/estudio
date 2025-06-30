// js/reservas/reservas.js

// 1) Función para cargar reservas
async function fetchReservations() {
  try {
    const res = await fetch('/estudio/Backend/reservas/get_reservas.php');
    const reservas = await res.json();
    
    const now = new Date();
    const nextWeek = new Date();
    nextWeek.setDate(now.getDate() + 7);

    const upcomingBody = document.getElementById('upcoming-appointments');
    const allBody      = document.getElementById('appointments-table');

    let upcomingHtml = '';
    let allHtml      = '';

    reservas.forEach(r => {
      const trAll = `
        <tr>
          <td>${r.nombre_cliente}</td>
          <td>${r.email_cliente}<br>${r.telefono_cliente}</td>
          <td>${r.servicio}</td>
          <td>${r.fecha}</td>
          <td>${r.hora}</td>
          <td>${r.estado || 'pendiente'}</td>
          <td>
            <button class="btn-small">✏️</button>
            <button class="btn-small">🗑️</button>
          </td>
        </tr>`;
      allHtml += trAll;

      const fechaReserva = new Date(r.fecha + 'T' + r.hora);
      if (fechaReserva >= now && fechaReserva <= nextWeek) {
        upcomingHtml += `
          <tr>
            <td>${r.nombre_cliente}</td>
            <td>${r.servicio}</td>
            <td>${r.fecha}</td>
            <td>${r.estado || 'pendiente'}</td>
          </tr>`;
      }
    });

    if (!upcomingHtml) {
      upcomingHtml = `<tr><td colspan="4">No hay citas en los próximos 7 días</td></tr>`;
    }
    if (!allHtml) {
      allHtml = `<tr><td colspan="7">No se encontraron reservas</td></tr>`;
    }

    upcomingBody.innerHTML = upcomingHtml;
    allBody.innerHTML      = allHtml;
    document.getElementById('total-appointments').textContent = reservas.length;
  } catch (e) {
    console.error('Error cargando reservas:', e);
    document.getElementById('upcoming-appointments').innerHTML =
      '<tr><td colspan="4">Error al cargar citas</td></tr>';
    document.getElementById('appointments-table').innerHTML =
      '<tr><td colspan="7">Error al cargar reservas</td></tr>';
  }
}

// 2) Inicialización
document.addEventListener('DOMContentLoaded', () => {
  fetchReservations();
});
