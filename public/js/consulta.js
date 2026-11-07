/* NutriFzst · Formulario de consulta: IMC en pantalla, medicamentos dinámicos y atajo de teclado.
   Es sólo ayuda visual: el IMC y la validación definitiva se calculan en Laravel. */
(function () {
  "use strict";

  var form = document.getElementById("form-consulta");
  if (!form) return;

  // IMC en pantalla
  var estatura = parseFloat(form.dataset.estatura), peso = document.getElementById("peso"), imc = document.getElementById("imc");
  function actualizarImc() {
    var p = parseFloat(peso.value);
    imc.value = (p > 0 && estatura > 0) ? (Math.round(p / (estatura * estatura) * 100) / 100).toFixed(2) : "";
  }
  peso.addEventListener("input", actualizarImc);
  actualizarImc();

  // Medicamentos
  var lista = document.getElementById("lista-medicamentos"), btn = document.getElementById("btn-agregar-med");
  var siguiente = btn ? parseInt(btn.dataset.siguiente, 10) || 0 : 0;

  lista.addEventListener("click", function (e) {
    if (e.target.matches("[data-quitar]")) e.target.closest(".nf-med").remove();
  });

  if (btn) btn.addEventListener("click", function () {
    var i = siguiente++;
    var div = document.createElement("div");
    div.className = "row g-2 align-items-end mb-2 nf-med";
    div.innerHTML =
      '<div class="col-md-4"><label class="form-label small">Medicamento</label><input class="form-control" name="medicamentos[' + i + '][nombre]" maxlength="120"></div>' +
      '<div class="col-6 col-md-3"><label class="form-label small">Dosis</label><input class="form-control" name="medicamentos[' + i + '][dosis]" maxlength="80"></div>' +
      '<div class="col-6 col-md-3"><label class="form-label small">Frecuencia</label><input class="form-control" name="medicamentos[' + i + '][frecuencia]" maxlength="80"></div>' +
      '<div class="col-md-2"><button type="button" class="btn btn-outline-secondary w-100" data-quitar>Quitar</button></div>';
    lista.appendChild(div);
    div.querySelector("input").focus();
  });

  // Ctrl + Enter guarda
  document.addEventListener("keydown", function (e) {
    if (e.key === "Enter" && (e.ctrlKey || e.metaKey)) form.requestSubmit();
  });

  // Si el servidor devolvió errores, lleva el foco al primero
  var primero = form.querySelector(".is-invalid");
  if (primero) { primero.scrollIntoView({ block: "center" }); primero.focus(); }
  else peso.focus();
})();
