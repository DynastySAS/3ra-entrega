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
    const res = await fetch(`${API_URL}/usuarios`, { method: "GET" });
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
      Aprobar
    </button>
    <button class="eliminar" onclick="eliminarUsuario(${usuario.id_usuario})">
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
    const res = await fetch(`${API_URL}/pagos`, { method: "GET" });
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
      Aprobar
    </button>
    <button class="eliminar" onclick="rechazarPago(${pago.id_pago})">
      Rechazar
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
  const res = await fetch(`${API_URL}/usuarios/${id}`, {
    method: "PUT"
  });
  const data = await res.json();
  alert(data.message);
  cargarUsuarios();
}


// ===Eliminar Usuario===
async function eliminarUsuario(id) {
  const res = await fetch(`${API_URL}/usuarios/${id}`, {
    method: "DELETE"
  });
  const data = await res.json();
  alert(data.message);
  cargarUsuarios();
}


// === Aprobar pago ===
async function aprobarPago(id) {
  const res = await fetch(`${API_URL}/pagos/${id}`, {
    method: "PUT"
  });
  const data = await res.json();
  alert(data.message);
  cargarPagos();
}

// ===Rechazar pago===
async function rechazarPago(id) {
  const res = await fetch(`${API_URL}/pagos/${id}`, {
    method: "DELETE"
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
