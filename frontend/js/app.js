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
  let listaAulas = document.getElementById("lista-aulas");

  if (select){
    select.innerHTML = "";
  }

  if (listaAulas) {
    listaAulas.innerHTML = "";
  }

  aulas.forEach((aula) => {
    if (select) {
        const opcion = document.createElement("option");
        opcion.value = aula.id;
        opcion.textContent = aula.nombre;
        select.appendChild(opcion);
    }

    if (listaAulas){
      const tarjeta = document.createElement("div");
      tarjeta.className = "tarjeta";
      tarjeta.innerHTML = `
        <h3>${aula.nombre}</h3>
        <p>ID: ${aula.id}</p>
      `;
      listaAulas.appendChild(tarjeta);
    }
    
  });
}

//muestro si esta en desarrollo o en produccion
function mostrarDesarrollo() {
  let ent = document.getElementById("entorno-app");
  if (window.APP_CONFIG && ent) {
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
      aula_id: aula,
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
      .then(async (res) => {
        const datos = await res.json();
        
        if ((!res.ok)) {
          throw new Error(datos.error || datos.message || "Error al hacer la reserva")
        }

        return datos
      })
      .then((data) => {
        mensaje.textContent = data.message; //se recibe bien la respuesta
        mensaje.className = "ok";
        formulario.reset();
        cargaReservas();
      })
      .catch((error) => {
        mensaje.textContent= error.message;
        mensaje.className = "error"
      })
  });
})();
