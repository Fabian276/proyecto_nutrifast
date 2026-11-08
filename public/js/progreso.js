/* NutriFzst · Pantalla de progreso. Los datos llegan de Laravel en <script type="application/json" id="nf-progreso-datos">. */
(function () {
  "use strict";

  var nodo = document.getElementById("nf-progreso-datos");
  var datos = nodo ? JSON.parse(nodo.textContent) : { indicadores: [], puntos: [] };
  var $ = function (s) { return document.querySelector(s); };
  var $$ = function (s) { return Array.prototype.slice.call(document.querySelectorAll(s)); };

  // "2026-07-06" -> "06/07/2026" (sin pasar por Date, para evitar desfases de zona horaria)
  function fecha(iso) { var p = iso.split("-"); return p[2] + "/" + p[1] + "/" + p[0]; }
  function signo(d) { return d > 0 ? "+" : d < 0 ? "−" : ""; }

  var puntos = datos.puntos;
  var disponibles = datos.indicadores.filter(function (i) { return puntos.some(function (c) { return c[i.k] != null; }); });
  var sel = $("#indicador"), comp = $("#comparar"), desde = $("#desde"), hasta = $("#hasta");
  var COLOR1 = "#2f7a63", COLOR2 = "#d9822b";

  function opcion(i) { return '<option value="' + i.k + '">' + i.n + (i.u ? " (" + i.u + ")" : "") + "</option>"; }
  function buscar(k) { return disponibles.filter(function (i) { return i.k === k; })[0]; }

  sel.innerHTML = disponibles.map(opcion).join("");
  comp.innerHTML = '<option value="">Ninguno</option>' + disponibles.map(opcion).join("");
  // Por defecto se compara contra grasa corporal (o masa muscular) si hay datos.
  comp.value = buscar("porcentaje_grasa") ? "porcentaje_grasa" : (buscar("masa_muscular") ? "masa_muscular" : "");
  desde.value = puntos[0].fecha;
  hasta.value = puntos[puntos.length - 1].fecha;

  var grafica = null, lienzo = $("#grafica");

  function enRango() {
    return puntos.filter(function (c) { return (!desde.value || c.fecha >= desde.value) && (!hasta.value || c.fecha <= hasta.value); });
  }

  function resumen(ind, pts, color) {
    var v0 = pts[0][ind.k], v1 = pts[pts.length - 1][ind.k], d = v1 - v0;
    var marca = color ? '<span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:' + color + ';margin-right:6px"></span>' : "";
    return marca + "<strong>" + ind.n + ":</strong> " + v0.toFixed(ind.d) + " → " + v1.toFixed(ind.d) + " " + ind.u + " " +
      '<span class="' + (d <= 0 ? "nf-delta-down" : "nf-delta-up") + ' fw-semibold">(' + signo(d) + Math.abs(d).toFixed(ind.d) + " " + ind.u + ")</span> en " + pts.length + " consultas";
  }

  function serie(ind, xs, color, eje, relleno) {
    return {
      label: ind.n, yAxisID: eje, data: xs.map(function (c) { return c[ind.k]; }), spanGaps: true,
      borderColor: color, backgroundColor: relleno ? "rgba(47,122,99,.12)" : color, fill: relleno, tension: .25,
      pointRadius: 5, pointHoverRadius: 7, pointBackgroundColor: color, borderWidth: 2.5, borderDash: relleno ? [] : [6, 4]
    };
  }

  function pintar() {
    var cs = enRango();
    var ind = buscar(sel.value) || disponibles[0];
    var ind2 = comp.value && comp.value !== ind.k ? buscar(comp.value) : null;
    var pts = cs.filter(function (c) { return c[ind.k] != null; });
    var pts2 = ind2 ? cs.filter(function (c) { return c[ind2.k] != null; }) : [];
    if (pts2.length < 2) ind2 = null;
    var hayDatos = pts.length >= 2;
    $("#progreso-vacio").classList.toggle("d-none", hayDatos);
    $("#bloque-grafica").classList.toggle("d-none", !hayDatos);

    if (hayDatos) {
      $("#resumen").innerHTML = resumen(ind, pts, ind2 ? COLOR1 : null) + (ind2 ? "<br>" + resumen(ind2, pts2, COLOR2) : "");
      if (window.Chart) {
        // Eje X: fechas donde cualquiera de las dos líneas tiene dato.
        var xs = ind2 ? cs.filter(function (c) { return c[ind.k] != null || c[ind2.k] != null; }) : pts;
        var series = [serie(ind, xs, COLOR1, "y", true)];
        if (ind2) series.push(serie(ind2, xs, COLOR2, "y2", false));

        var escalas = {
          x: { grid: { display: false } },
          y: { position: "left", title: { display: !!(ind.u || ind2), text: ind2 ? ind.n + (ind.u ? " (" + ind.u + ")" : "") : ind.u, color: ind2 ? COLOR1 : undefined }, grid: { color: "#eef2f0" } }
        };
        if (ind2) escalas.y2 = { position: "right", title: { display: true, text: ind2.n + (ind2.u ? " (" + ind2.u + ")" : ""), color: COLOR2 }, grid: { drawOnChartArea: false } };

        if (grafica) grafica.destroy();
        grafica = new Chart(lienzo, {
          type: "line",
          data: { labels: xs.map(function (c) { return fecha(c.fecha); }), datasets: series },
          options: {
            responsive: true, maintainAspectRatio: false, interaction: { mode: "index", intersect: false },
            plugins: {
              legend: { display: !!ind2, position: "bottom", labels: { usePointStyle: true } },
              tooltip: { callbacks: { label: function (c) { var i = c.datasetIndex === 0 ? ind : ind2; return " " + i.n + ": " + c.parsed.y.toFixed(i.d) + " " + i.u; } } }
            },
            scales: escalas
          }
        });
        lienzo.setAttribute("aria-label", "Gráfica de " + ind.n + (ind2 ? " comparado con " + ind2.n : "") + " por fecha");
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

  [sel, comp, desde, hasta].forEach(function (el) { el.addEventListener("change", pintar); });
  $$("[data-vista]").forEach(function (b) { b.addEventListener("click", function () { vista(b.dataset.vista); }); });
  pintar(); vista("grafica");
})();
