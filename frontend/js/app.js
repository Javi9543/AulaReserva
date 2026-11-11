//Pido aulas al php
function cargarAulas(url, aulas) {
  fetch(url)
    .then((response) => response.json())
    .then((data) => aulas(data))
    .catch((error) => console.error("Error al cargar aulas:", error));
}

//muestro las aulas
function mostrarAulas(aulas) {
  let select = document.getElementById("aula");

  aulas.forEach((aula) => {
    const option = document.createElement("option");
    option.value = aula;
    option.textContent = aula;
    select.appendChild(option);
  });
}

//muestro si esta en desarrollo o en produccion
function mostrarDesarrollo() {
  let ent = document.getElementById("entorno-app");
  if (window.APP_CONFIG) {
    ent.textContent =
      window.APP_CONFIG.env === "production" ? "Producción" : "Desarrollo";
  }
}

//cargo reservas
function cargaReservas() {
  fetch("http://localhost:8000/api/reservas")
    .then((res) => res.json())
    .then((numReserva) => {
      let cuerpo = document.getElementById("cuerpo-reservas");
      let contador = document.getElementById("contador-reservas");

      cuerpo.innerHTML = "";
      contador.textContent = numReserva.length;

      numReserva.forEach((reserva) => {
        let fila = document.createElement("tr");
        fila.innerHTML = `
          <td>${reserva.aula_id}</td>
          <td>${reserva.fecha}</td>
          <td>${reserva.hora}</td>
          <td>${reserva.responsable}</td>
          <td><button onclick="cancelarReserva(${reserva.id})">Cancelar</button></td>
        `;
        cuerpo.appendChild(fila);
      });
  });
}

function cancelarReserva(id) {
  fetch (`http://localhost:8000/api/reservas/${id}`, { method: "DELETE" })
    .then((res) => res.json())
    .then(() => cargaReservas());
}

(function () {
  cargarAulas("http://localhost:8000/api/aulas", mostrarAulas);
  mostrarDesarrollo();
  cargaReservas();

  const formulario = document.getElementById("formulario-reserva");
  const mensaje = document.getElementById("mensaje");

  formulario.addEventListener("submit", (e) => {
    e.preventDefault();

    const aula = document.getElementById("aula").value;
    const fecha = document.getElementById("fecha").value;
    const hora = document.getElementById("hora").value;
    const usuario = document.getElementById("usuario").value.trim();

    if (!aula || !fecha || !hora || !usuario) {
      mensaje.textContent = "Todos los campos son obligatorios.";
      mensaje.className = "error";
      return;
    }

    //proceso los datos para el php
    let reserva = {
      aula: aula,
      fecha: fecha,
      hora: hora,
      responsable: usuario,
    };

    //hago la peticion POST al index.php
    fetch("http://localhost:8000/api/reservas", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(reserva),
    })
      .then((response) => response.json())
      .then((data) => {
        mensaje.textContent = data.message; //se recibe bien la respuesta
        mensaje.className = "ok";
        formulario.reset();
        cargaReservas();
      });
  });
})();
