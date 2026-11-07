/* NutriFzst · JavaScript común (presentación). La lógica de negocio vive en Laravel. */
(function () {
  "use strict";

  document.addEventListener("DOMContentLoaded", function () {
    // Aviso (toast) con el mensaje flash de la sesión
    var toast = document.getElementById("nf-toast");
    if (toast && toast.dataset.aviso && window.bootstrap) {
      toast.querySelector(".toast-body").textContent = toast.dataset.aviso;
      bootstrap.Toast.getOrCreateInstance(toast, { delay: 2600 }).show();
    }

    // Filas de tabla que abren su enlace con clic o Enter
    document.querySelectorAll("tr[data-href]").forEach(function (tr) {
      tr.addEventListener("click", function (e) { if (!e.target.closest("a")) location.href = tr.dataset.href; });
      tr.addEventListener("keydown", function (e) { if (e.key === "Enter") location.href = tr.dataset.href; });
    });

    // Modal de paciente (alta/edición)
    var modalEl = document.getElementById("modal-paciente");
    if (modalEl && window.bootstrap) {
      var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modalEl.addEventListener("shown.bs.modal", function () {
        var n = document.getElementById("p-nombre"); if (n) n.focus();
      });
      document.querySelectorAll("[data-abrir-modal]").forEach(function (b) {
        b.addEventListener("click", function () {
          var texto = (b.dataset.prellenar || "").trim();
          var nombre = document.getElementById("p-nombre"), apellidos = document.getElementById("p-apellidos");
          if (texto && nombre && apellidos && !nombre.value && !apellidos.value) {
            var partes = texto.split(/\s+/);
            nombre.value = partes.shift() || "";
            apellidos.value = partes.join(" ");
          }
          modal.show();
        });
      });
      // Si la validación del servidor falló, se reabre con los mensajes
      if (modalEl.hasAttribute("data-reabrir")) modal.show();
    }

    // Búsqueda de pacientes: envía el formulario al dejar de escribir
    var buscar = document.getElementById("buscar");
    if (buscar) {
      var t;
      buscar.addEventListener("input", function () {
        clearTimeout(t);
        t = setTimeout(function () { buscar.form.submit(); }, 400);
      });
      if (buscar.value) { buscar.focus(); buscar.setSelectionRange(buscar.value.length, buscar.value.length); }
      else buscar.focus();
    }
  });
})();
