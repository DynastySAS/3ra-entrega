// === Navegación ===
function mostrar(id) {
  document.querySelectorAll(".content, .login-box, .register-box").forEach((div) => {
    div.classList.remove("active");
  });
  const seccion = document.getElementById(id);
  if (seccion) seccion.classList.add("active");
}

// === Toggle password ===
function togglePassword(inputId) {
  const input = document.getElementById(inputId);
  input.type = input.type === "password" ? "text" : "password";
}

// === Login ===
async function handleLogin(e) {
  e.preventDefault();
  const form = e.target;
  const data = {
    identificador: form.identificador.value,
    password: form.password.value,
  };

  try {
      const res = await fetch("../Backend/Apis/usuario.php?action=login", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(data),
    });

    const result = await res.json().catch(() => ({})); // por si no hay body válido

    if (res.ok && result.success) {
      const usuario = result.usuario;
      localStorage.setItem("usuario", JSON.stringify(usuario));
      window.location.href =
        usuario.rol === "administrador"
          ? "Backoffice/backoffice.html"
          : "cooperativista.html";
    } else {
      alert(result.message || "Usuario no encontrado o pendiente de aprobación");
    }
  } catch (err) {
    console.error("Error en login:", err);
    alert("Error de conexión con el servidor");
  }
  return false;
}

// === Registro ===
async function handleRegisterForm(e) {
  e.preventDefault();
  const form = e.target;
  const data = {
    nombre: form.querySelector("#nombre").value,
    apellido: form.querySelector("#apellido").value,
    email_cont: form.querySelector("#email_cont").value,
    usuario_login: form.querySelector("#usuario_login").value,
    id_persona: form.querySelector("#id_persona").value,
    password: form.querySelector("#registro-password").value,
  };

  try {
    const res = await fetch("../Backend/Apis/usuario.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    const result = await res.json();
    if (result.success) {
      alert("Registro de usuario solicitado con éxito");
      mostrar("login");
    } else {
      alert(result.message || "Error al registrarse");
    }
  } catch (err) {
    console.error(err);
    alert("Error de conexión");
  }
  return false;
}

// === Panel de usuario ===
document.addEventListener("DOMContentLoaded", () => {
  const usuario = JSON.parse(localStorage.getItem("usuario"));
  if (document.getElementById("form-info") && usuario) {
    cargarInfoPersonal(usuario.id_usuario);

    document.getElementById("form-info").addEventListener("submit", actualizarInfoPersonal);
    document.getElementById("form-horas").addEventListener("submit", registrarHoras);
    document.getElementById("form-pago").addEventListener("submit", registrarPago);
  }
});

// === Cargar datos ===
async function cargarInfoPersonal(idUsuario) {
  try {
    // llamamos al endpoint RESTful con action=usuario y parametro id (que coopera con el router)
    const res = await fetch(`../Backend/Apis/cooperativa.php?action=usuario&id=${idUsuario}`);
    const data = await res.json();
    if (data.success && data.data) {
      const u = data.data;
      document.getElementById("nombre").value = u.nombre || "";
      document.getElementById("apellido").value = u.apellido || "";
      document.getElementById("id_usuario").value = u.id_usuario || "";
      document.getElementById("usuario_login").value = u.usuario_login || "";
      document.getElementById("id_persona").value = u.id_persona || "";
      document.getElementById("telefono").value = u.telefono_cont || "";
      document.getElementById("email").value = u.email_cont || "";

      // asegúrate que los inputs del formulario tengan estos IDs (los usaremos en los submits)
      document.getElementById("horas-id-usuario").value = u.id_usuario || "";
      document.getElementById("pago-id-usuario").value = u.id_usuario || "";
    } else {
      console.warn("Usuario no encontrado o error:", data.message);
    }
  } catch (err) {
    console.error("Error al cargar info personal:", err);
  }
}

// === Actualizar perfil ===
async function actualizarInfoPersonal(e) {
  e.preventDefault();

  const data = {
    id_usuario: document.getElementById("id_usuario").value,
    usuario_login: document.getElementById("usuario_login").value,
    telefono_cont: document.getElementById("telefono").value,
    email_cont: document.getElementById("email").value,
  };

  try {
    const res = await fetch("../Backend/Apis/usuario.php", {
      method: "PUT",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });

    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    const result = await res.json();

    alert(result.message || (result.success ? "Perfil actualizado" : "Error al actualizar"));
  } catch (err) {
    console.error("Error al actualizar perfil:", err);
    alert("Error de conexión al actualizar perfil");
  }
}


// === Registrar horas ===
async function registrarHoras(e) {
  e.preventDefault();
  const form = e.target;

  // tomar el id_usuario del input específico que cargamos
  const idUsuario = document.getElementById("horas-id-usuario")?.value || form["id_usuario"]?.value;

  const fechaVal = form["fecha"] ? form["fecha"].value : null;
  const semanaIso = fechaVal ? new Date(fechaVal).toISOString().slice(0, 10) : null;

  const data = {
    id_usuario: idUsuario,
    horas_cumplidas: form["horas"] ? form["horas"].value : null,
    motivo: form["motivo"] ? form["motivo"].value : null,
    semana: semanaIso,
    id_registro: null
  };

  try {
    const res = await fetch("../Backend/Apis/cooperativa.php?action=trabajo", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    const json = await res.json();
    alert(json.message || (json.success ? "Horas registradas" : "Error al registrar horas"));
  } catch (err) {
    console.error("Error registrar horas:", err);
    alert("Error de red al registrar horas");
  }
}

// === Registrar pago ===
async function registrarPago(e) {
  e.preventDefault();
  const form = e.target;

  // leemos el input que cargamos
  const idUsuario = document.getElementById("pago-id-usuario")?.value || form["id_usuario"]?.value;

  const data = {
    id_usuario: idUsuario,
    tipo_pago: form["tipo-pago"] ? form["tipo-pago"].value : null,
    monto: form["monto"] ? form["monto"].value : null,
  };

  try {
    const res = await fetch("../Backend/Apis/cooperativa.php?action=pago", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify(data),
    });
    const json = await res.json();
    alert(json.message || (json.success ? "Pago registrado" : "Error al registrar pago"));
  } catch (err) {
    console.error("Error registrar pago:", err);
    alert("Error de red al registrar pago");
  }
}

// === Cerrar sesión ===
function cerrarSesion() {
  localStorage.removeItem("usuario");
  window.location.href = "index.html";
}
