// en js/admin/blog.js (o donde centralices tu JS de dashboard)
document.getElementById('blog-form').addEventListener('submit', async e => {
  e.preventDefault();
  const form = e.target;
  const data = {
    titulo:    form.title.value.trim(),
    contenido: form.content.value.trim(),
    imagen:    form.featured_image.value || null,   // si implementas subida, ajusta luego
    categoria: form.category.value,
    estado:    form.status.value
  };
  try {
    const res  = await fetch('/estudio/Backend/blog/guardar_blog.php',{
      method:'POST',
      headers:{'Content-Type':'application/json'},
      body: JSON.stringify(data)
    });
    const json = await res.json();
    if(json.success){
      alert('Entrada guardada');
      form.reset();
      // Opcional: recargar lista de entradas en el dashboard
    } else {
      alert('Error: '+json.error);
    }
  } catch(err){
    alert('Error de red: '+err.message);
  }
});
