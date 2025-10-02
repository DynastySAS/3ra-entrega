const API_URL = "../../Backend/Apis/backoffice.php";

// === Navegación entre secciones ===
function mostrar(id) {
  document.querySelectorAll(".content").forEach(div => {
    div.classList.remove("active");
  });
  const seccion = document.getElementById(id);
  if (seccion) seccion.classList.add("active");
}

// === Listar usuarios pendientes ===
async function cargarUsuarios() {
  try {
    const res = await fetch(`${API_URL}?action=usuario`);
    const data = await res.json();
    const contenedor = document.getElementById("usuarios");
    contenedor.innerHTML = "";

    if (!data.data || !Array.isArray(data.data)) {
      contenedor.innerHTML = "<p>No hay usuarios pendientes.</p>";
      return;
  }
  data.data.forEach(usuario => {
  if (usuario.estado === "solicitado")  {
        const div = document.createElement("div");
        div.classList.add("item");
        div.innerHTML = `
          <div class="card">
    <h3>${usuario.nombre} ${usuario.apellido}</h3>
    <p><b>Id Usuario:</b> ${usuario.id_usuario}</p>
    <p><b>Email:</b> ${usuario.email_cont}</p>
    <p><b>Usuario:<b/> ${usuario.usuario_login}</p>
    <p><b>Estado:</b> ${usuario.estado}</p>
    <button class="btn" onclick="aprobarUsuario(${usuario.id_usuario})">
      ✅ Aprobar
    </button>
    <button class="btn" onclick="eliminarUsuario(${usuario.id_usuario})">
     Eliminar
    </button>
  </div>
        `;
        contenedor.appendChild(div);
      }
    });
  } catch (err) {
    console.error("Error cargando usuarios:", err);
  }
}

// === Listar pagos pendientes ===
async function cargarPagos() {
  try {
    const res = await fetch(`${API_URL}?action=pago`);
    const data = await res.json();
    const contenedor = document.getElementById("pagos");
    contenedor.innerHTML = "";

    if (!data.data || !Array.isArray(data.data)) {
      contenedor.innerHTML = "<p>No hay pagos pendientes.</p>";
      return;
    }

    data.data.forEach(pago => {
      if (pago.estado === "solicitado") {
        const div = document.createElement("div");
        div.classList.add("item");
        div.innerHTML = `
          <div class="card">
    <h3>Pago #${pago.id_pago}</h3>
    <p><b>Monto:</b> $${pago.monto}</p>
    <p><b>Usuario:</b> ${pago.id_usuario}</p>
    <p><b>Estado:</b> ${pago.estado}</p>
    <button class="btn" onclick="aprobarPago(${pago.id_pago})">
      ✅ Aprobar
    </button>
  </div>
        `;
        contenedor.appendChild(div);
      }
    });
  } catch (err) {
    console.error("Error cargando pagos:", err);
  }
}

// === Aprobar usuario ===
async function aprobarUsuario(id) {
  try {
    const res = await fetch(`${API_URL}?action=usuario&id=${id}`, {
      method: "PUT",
    });
    const data = await res.json();
    alert(data.message);
    cargarUsuarios();
  } catch (err) {
    console.error("Error aprobando usuario:", err);
  }
}

// Eliminar Usuario
async function eliminarUsuario(id) {
  try {
    const res = await fetch(`${API_URL}?action=usuario&id=${id}`, {
      method: "DELETE",
    });
    const data = await res.json();
    alert(data.message);
    cargarUsuarios();
  } catch (err) {
    console.error("Error eliminando usuario:", err);
  }
}

// === Aprobar pago ===
async function aprobarPago(id) {
  const res = await fetch(`${API_URL}?action=pago&id=${id}`, {
    method: "PUT",
  });
  const data = await res.json();
  alert(data.message);
  cargarPagos();
}



// === Simulación de cierre de sesión ===
function cerrarSesion() {
  localStorage.removeItem("usuario");
  window.location.href = "../index.html";
}

// === Inicializar ===
window.onload = () => {
  cargarUsuarios();
  cargarPagos();
};
