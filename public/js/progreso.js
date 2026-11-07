/* NutriFzst · Pantalla de progreso. Los datos llegan de Laravel en window.NF_PROGRESO. */
(function () {
  "use strict";

  var datos = window.NF_PROGRESO || { indicadores: [], puntos: [] };
  var $ = function (s) { return document.querySelector(s); };
  var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };

  // "2026-07-06" -> "06/07/2026" (sin pasar por Date, para evitar desfases de zona horaria)
  function fecha(iso) { var p = iso.split("-"); return p[2] + "/" + p[1] + "/" + p[0]; }
  function signo(d) { return d > 0 ? "+" : d < 0 ? "−" : ""; }

  var puntos = datos.puntos;
  var disponibles = datos.indicadores.filter(function (i) { return puntos.some(function (c) { return c[i.k] != null; }); });
  var sel = $("#indicador"), desde = $("#desde"), hasta = $("#hasta");

  sel.innerHTML = disponibles.map(function (i) {
    return '<option value="' + i.k + '">' + i.n + (i.u ? " (" + i.u + ")" : "") + "</option>";
  }).join("");
  desde.value = puntos[0].fecha;
  hasta.value = puntos[puntos.length - 1].fecha;

  var grafica = null, lienzo = $("#grafica");

  function enRango() {
    return puntos.filter(function (c) { return (!desde.value || c.fecha >= desde.value) && (!hasta.value || c.fecha <= hasta.value); });
  }

  function pintar() {
    var cs = enRango();
    var ind = disponibles.filter(function (i) { return i.k === sel.value; })[0] || disponibles[0];
    var pts = cs.filter(function (c) { return c[ind.k] != null; });
    var hayDatos = pts.length >= 2;
    $("#progreso-vacio").classList.toggle("d-none", hayDatos);
    $("#bloque-grafica").classList.toggle("d-none", !hayDatos);

    if (hayDatos) {
      var v0 = pts[0][ind.k], v1 = pts[pts.length - 1][ind.k], d = v1 - v0;
      $("#resumen").innerHTML = "<strong>" + ind.n + ":</strong> " + v0.toFixed(ind.d) + " → " + v1.toFixed(ind.d) + " " + ind.u + " " +
        '<span class="' + (d <= 0 ? "nf-delta-down" : "nf-delta-up") + ' fw-semibold">(' + signo(d) + Math.abs(d).toFixed(ind.d) + " " + ind.u + ")</span> en " + pts.length + " consultas";
      if (window.Chart) {
        if (grafica) grafica.destroy();
        grafica = new Chart(lienzo, {
          type: "line",
          data: { labels: pts.map(function (c) { return fecha(c.fecha); }), datasets: [{
            label: ind.n, data: pts.map(function (c) { return c[ind.k]; }), borderColor: "#2f7a63", backgroundColor: "rgba(47,122,99,.12)",
            fill: true, tension: .25, pointRadius: 5, pointHoverRadius: 7, pointBackgroundColor: "#2f7a63", borderWidth: 2.5 }] },
          options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: function (c) { return " " + c.parsed.y.toFixed(ind.d) + " " + ind.u; } } } },
            scales: { y: { title: { display: !!ind.u, text: ind.u }, grid: { color: "#eef2f0" } }, x: { grid: { display: false } } }
          }
        });
        lienzo.setAttribute("aria-label", "Gráfica de " + ind.n + " por fecha");
      }
    }

    // Tabla comparativa: todos los indicadores con datos, una columna por fecha
    $("#tabla-comparativa thead").innerHTML = '<tr><th>Indicador</th>' +
      cs.map(function (c) { return '<th class="text-end">' + fecha(c.fecha) + "</th>"; }).join("") + '<th class="text-end">Cambio</th></tr>';
    $("#tabla-comparativa tbody").innerHTML = disponibles.map(function (i) {
      var vals = cs.map(function (c) { return c[i.k]; });
      var con = vals.filter(function (v) { return v != null; });
      var d = con.length >= 2 ? con[con.length - 1] - con[0] : null;
      return "<tr" + (i.k === ind.k ? ' class="table-active"' : "") + "><td>" + i.n + (i.u ? ' <span class="text-body-secondary small">(' + i.u + ")</span>" : "") + "</td>" +
        vals.map(function (v) { return '<td class="num text-end">' + (v != null ? v.toFixed(i.d) : "—") + "</td>"; }).join("") +
        '<td class="num text-end fw-semibold">' + (d == null ? "—" : signo(d) + Math.abs(d).toFixed(i.d)) + "</td></tr>";
    }).join("");
  }

  function vista(cual) {
    $("#vista-grafica").classList.toggle("d-none", cual !== "grafica");
    $("#vista-tabla").classList.toggle("d-none", cual !== "tabla");
    $$("[data-vista]").forEach(function (b) {
      var a = b.dataset.vista === cual;
      b.classList.toggle("btn-primary", a); b.classList.toggle("btn-outline-primary", !a); b.setAttribute("aria-pressed", a);
    });
  }

  [sel, desde, hasta].forEach(function (el) { el.addEventListener("change", pintar); });
  $$("[data-vista]").forEach(function (b) { b.addEventListener("click", function () { vista(b.dataset.vista); }); });
  pintar(); vista("grafica");
})();
