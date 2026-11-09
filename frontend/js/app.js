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

(function () {
  cargarAulas("http://localhost:8000/index.php", mostrarAulas);

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
    fetch("http://localhost:8000/index.php", {
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
      });

    mensaje.textContent = `Reserva realizada: ${aula} el ${fecha} a las ${hora} por ${usuario}`;
    mensaje.className = "ok";
  });
})();
