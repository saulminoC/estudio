// js/admin/blog.js

/**
 * Carga y muestra las entradas de blog en la tabla del Dashboard
 */
async function loadBlogEntries() {
  try {
    const res = await fetch('/estudio/Backend/blog/get_blog.php');
    const posts = await res.json();
    const tbody = document.getElementById('blog-table');
    if (!Array.isArray(posts)) throw new Error(posts.error || 'Respuesta inválida');

    tbody.innerHTML = posts.map(p => `
      <tr>
        <td>${p.title}</td>
        <td>${p.date}</td>
        <td>${p.status}</td>
        <td><!-- Vistas o métricas --></td>
        <td>
          <button class="btn-small">✏️</button>
          <button class="btn-small">🗑️</button>
        </td>
      </tr>
    `).join('');

  } catch (e) {
    console.error('No se pudieron cargar las entradas de blog:', e);
    document.getElementById('blog-table').innerHTML =
      '<tr><td colspan="5">Error cargando entradas</td></tr>';
  }
}

/**
 * Envía el formulario del modal para guardar una nueva entrada
 */
document.addEventListener('DOMContentLoaded', () => {
  // Al cargar el Dashboard inicializamos la tabla
  loadBlogEntries();

  // Listener del formulario
  const form = document.getElementById('blog-form');
  form.addEventListener('submit', async e => {
    e.preventDefault();
    const fd = new FormData(form);
    const data = {
      title:    fd.get('title').trim(),
      content:  fd.get('content').trim(),
      image:    fd.get('image')?.trim() || null,
      category: fd.get('category') || null,
      status:   fd.get('status')
    };

    if (!data.title || !data.content) {
      return alert('Título y contenido son obligatorios');
    }

    try {
      const res = await fetch('/estudio/Backend/blog/guardar_blog.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
      });
      const json = await res.json();
      if (json.success) {
        closeModal('blogModal');
        form.reset();
        loadBlogEntries();
      } else {
        alert('Error: ' + json.error);
      }
    } catch (err) {
      console.error('Error de red al guardar blog:', err);
      alert('Error de red: ' + err.message);
    }
  });

  // Si se cambia a la sección Blog en el menú, recargamos
  document.querySelectorAll('[data-section="blog"]').forEach(link => {
    link.addEventListener('click', () => {
      loadBlogEntries();
    });
  });
});
