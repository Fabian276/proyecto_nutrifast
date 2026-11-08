<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>@yield('title')</title>
<style>
  @page { margin: 28px 36px 48px; }
  body { font-family: "DejaVu Sans", sans-serif; font-size: 11px; color: #1f2a26; line-height: 1.45; }
  .head { border-bottom: 3px solid #2f7a63; padding-bottom: 8px; margin-bottom: 14px; }
  .brand { font-size: 18px; font-weight: bold; color: #2f7a63; }
  .doc-title { font-size: 13px; color: #555; }
  .head-right { text-align: right; font-size: 10px; color: #555; }
  h2 { font-size: 13px; color: #2f7a63; border-bottom: 1px solid #d6e5df; padding-bottom: 3px; margin: 16px 0 6px; }
  table { width: 100%; border-collapse: collapse; }
  .grid td { padding: 3px 6px 3px 0; vertical-align: top; }
  .label { color: #666; width: 30%; }
  .data th { background: #e6f2ed; text-align: left; padding: 5px 6px; font-size: 10px; }
  .data td { border-bottom: 1px solid #e3e3e3; padding: 5px 6px; vertical-align: top; }
  .num { text-align: right; }
  .box { background: #f6faf8; border: 1px solid #d6e5df; border-radius: 4px; padding: 8px 10px; white-space: pre-line; }
  .muted { color: #888; }
  .firma { margin-top: 50px; width: 45%; border-top: 1px solid #555; text-align: center; padding-top: 4px; }
  .foot { position: fixed; bottom: -30px; left: 0; right: 0; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
  <div class="foot">NutriFzst · Generado el {{ now()->format('d/m/Y H:i') }}</div>
  <table class="head"><tr>
    <td><div class="brand">NutriFzst</div><div class="doc-title">@yield('title')</div></td>
    <td class="head-right">{{ $paciente->nombre_completo }}<br>{{ $paciente->edad }} años · {{ $paciente->sexo === 'F' ? 'Femenino' : 'Masculino' }} · {{ number_format($paciente->estatura_m, 2) }} m</td>
  </tr></table>

  @yield('content')
</body>
</html>
