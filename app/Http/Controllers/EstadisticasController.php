<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Arqueo;
use App\Models\Facturacion;
use App\Models\DetalleFactura;
use App\Models\Cliente;
use App\Models\Mecanico;
use Carbon\Carbon;
use ConsoleTVs\Charts\Classes\Chartjs\Chart;
use Illuminate\Http\Request;

class EstadisticasController extends Controller
{
    public function index(Request $request)
    {
        $tipo   = $request->get('tipo', 'month');
        $mes    = $request->get('mes');
        $semana = $request->get('semana');

        // 1) Opciones para selector de meses (últimos 12 meses)
        $opMes = [];
        for ($i = 0; $i < 12; $i++) {
            $dt = Carbon::now()->startOfMonth()->subMonths($i);
            $opMes[$dt->format('Y-m')] = $dt->locale('es')->translatedFormat('F Y');
        }

        // 2) Si estamos en "week" y hay mes seleccionado, armamos las semanas
        $opSem = [];
        if ($tipo === 'week' && $mes) {
            [$y, $m] = explode('-', $mes);
            $startM  = Carbon::create($y, $m, 1);
            $endM    = $startM->copy()->endOfMonth();
            $cursor  = $startM->copy()->startOfWeek();

            while ($cursor <= $endM) {
                $ini = $cursor->copy();
                $fin = $cursor->copy()->endOfWeek();
                if ($ini < $startM) $ini = $startM->copy();
                if ($fin > $endM)   $fin = $endM->copy();

                $key = $ini->format('o-\WW');
                $label = ucfirst(
                    $ini->locale('es')->translatedFormat('j \\d\\e F')
                    . ' – '
                    . $fin->locale('es')->translatedFormat('j \\d\\e F')
                );

                $opSem[$key] = $label;
                $cursor->addWeek();
            }
        }

        // 3) Inicializamos arrays para los gráficos de Ventas/Ganancias
        $labels         = [];
        $ventasData     = [];
        $gananciasData  = [];

        // Variables globales para KPI
        $totalUnidadesVendidas  = 0;
        $totalVentasPeriodo     = 0.0;
        $totalGananciasPeriodo  = 0.0;
        $manoObraTotal          = 0.0;
        $descuentoTotal         = 0.0;
        $facturasConDescuento   = 0;

        // Rango de fechas
        $inicioPeriodo = null;
        $finPeriodo    = null;

        // 4) Rellenar datos día a día (mes) o día a día (semana)
        if ($tipo === 'month' && $mes) {
            [$y, $m] = explode('-', $mes);
            $d = Carbon::create($y, $m, 1);

            $inicioPeriodo = $d->copy()->startOfMonth()->startOfDay();
            $finPeriodo    = $d->copy()->endOfMonth()->endOfDay();

            for ($day = 1; $day <= $d->daysInMonth; $day++) {
                $fecha = $d->copy()->day($day);
                $labels[] = $fecha->format('d');

                // Venta del Día
                $totalesArqueo = Arqueo::whereDate('fecha', $fecha)
                    ->whereNotNull('monto_final')
                    ->get()
                    ->sum(fn($a) => $a->diferencia);
                $ventasData[] = round($totalesArqueo, 2);
                $totalVentasPeriodo += round($totalesArqueo, 2);

                // Ganancia Diaria + Mano de Obra + Descuento
                $facturasDiarias = Facturacion::with('detalles.producto.detallesCompras')
                    ->whereDate('fecha', $fecha)
                    ->get();

                $gananciaDiaria = 0;
                foreach ($facturasDiarias as $factura) {
                    $manoObraTotal += $factura->mano_obra;

                    if ($factura->descuento > 0) {
                        $descuentoTotal += $factura->descuento;
                        $facturasConDescuento++;
                    }

                    foreach ($factura->detalles as $detalle) {
                        $producto = $detalle->producto;
                        $precioVenta          = $producto->precio_venta;
                        $precioCompraPromedio = (float) $producto
                            ->detallesCompras
                            ->avg('precio_unitario') ?: 0;
                        $cantidadVendida = $detalle->cantidad;

                        $gananciaDiaria += ($precioVenta - $precioCompraPromedio) * $cantidadVendida;
                        $totalUnidadesVendidas += $cantidadVendida;
                    }
                }

                $gananciasData[] = round($gananciaDiaria, 2);
                $totalGananciasPeriodo += round($gananciaDiaria, 2);
            }
        }
        elseif ($tipo === 'week' && $semana) {
            [$y, $w] = explode('-W', $semana);
            $inicioSemana = Carbon::now()->setISODate($y, $w)->startOfWeek();
            $finSemana    = Carbon::now()->setISODate($y, $w)->endOfWeek();

            $inicioPeriodo = $inicioSemana->copy()->startOfDay();
            $finPeriodo    = $finSemana->copy()->endOfDay();

            for ($i = 0; $i < 7; $i++) {
                $dia = $inicioSemana->copy()->addDays($i);
                $labels[] = $dia->locale('es')->format('D d');

                // Venta del Día
                $totalesArqueo = Arqueo::whereDate('fecha', $dia)
                    ->whereNotNull('monto_final')
                    ->get()
                    ->sum(fn($a) => $a->diferencia);
                $ventasData[] = round($totalesArqueo, 2);
                $totalVentasPeriodo += round($totalesArqueo, 2);

                // Ganancia Diaria + Mano de Obra + Descuento
                $facturasDiarias = Facturacion::with('detalles.producto.detallesCompras')
                    ->whereDate('fecha', $dia)
                    ->get();

                $gananciaDiaria = 0;
                foreach ($facturasDiarias as $factura) {
                    $manoObraTotal += $factura->mano_obra;

                    if ($factura->descuento > 0) {
                        $descuentoTotal += $factura->descuento;
                        $facturasConDescuento++;
                    }

                    foreach ($factura->detalles as $detalle) {
                        $producto = $detalle->producto;
                        $precioVenta          = $producto->precio_venta;
                        $precioCompraPromedio = (float) $producto
                            ->detallesCompras
                            ->avg('precio_unitario') ?: 0;
                        $cantidadVendida      = $detalle->cantidad;

                        $gananciaDiaria += ($precioVenta - $precioCompraPromedio) * $cantidadVendida;
                        $totalUnidadesVendidas += $cantidadVendida;
                    }
                }

                $gananciasData[] = round($gananciaDiaria, 2);
                $totalGananciasPeriodo += round($gananciaDiaria, 2);
            }
        }

        // 5) Gráfico: Ventas vs Ganancias
        $chartVentasYGanancias = new Chart();
        $chartVentasYGanancias->labels($labels);
        $chartVentasYGanancias->dataset(
            $tipo === 'week'
                ? 'Venta del Día (semana)'
                : 'Venta del Día (mes)',
            $tipo === 'week' ? 'bar' : 'line',
            $ventasData
        );
        $chartVentasYGanancias->dataset(
            $tipo === 'week'
                ? 'Ganancia Diaria (semana)'
                : 'Ganancia Diaria (mes)',
            $tipo === 'week' ? 'bar' : 'line',
            $gananciasData
        );
        $chartVentasYGanancias->options([
            'responsive'            => true,
            'maintainAspectRatio'   => false,
        ]);

        // 6) Gráfico: Productos Vendidos por Categoría
        $categoriasCounts = [];
        $categoriasLabels = [];
        $categoriasData   = [];

        if ($inicioPeriodo && $finPeriodo) {
            $detallesVendidos = DetalleFactura::with('producto.categoria')
                ->whereHas('factura', function($q) use ($inicioPeriodo, $finPeriodo) {
                    $q->whereBetween('fecha', [$inicioPeriodo, $finPeriodo]);
                })
                ->get();

            foreach ($detallesVendidos as $detalle) {
                $catNombre = $detalle->producto->categoria->nombre_categoria;
                $categoriasCounts[$catNombre] = 
                    ($categoriasCounts[$catNombre] ?? 0) + $detalle->cantidad;
            }

            foreach ($categoriasCounts as $cat => $count) {
                $categoriasLabels[] = $cat;
                $categoriasData[]   = $count;
            }
        }

        $chartCategorias = new Chart();
        $chartCategorias->labels($categoriasLabels);
        $chartCategorias->dataset(
            'Productos Vendidos por Categoría',
            'pie',
            $categoriasData
        );
        $chartCategorias->options([
            'responsive'            => true,
            'maintainAspectRatio'   => false,
        ]);

        // 7) Estadísticas adicionales: Cliente con más facturas
        $clienteMasVisitas   = null;
        $visitasClienteMax   = 0;
        $clientesLabels      = [];
        $clientesData        = [];
        if ($inicioPeriodo && $finPeriodo) {
            // Top 1 Cliente
            $topCliente = DB::table('facturacion')
                ->select('id_cliente', DB::raw('COUNT(*) as visitas'))
                ->whereBetween('fecha', [$inicioPeriodo, $finPeriodo])
                ->groupBy('id_cliente')
                ->orderByDesc('visitas')
                ->first();

            if ($topCliente) {
                $cli = Cliente::find($topCliente->id_cliente);
                if ($cli) {
                    $clienteMasVisitas = $cli->nombre;
                    $visitasClienteMax = $topCliente->visitas;
                }
            }

            // Gráfico de todos los clientes (barras)
            $todosClientes = DB::table('facturacion')
                ->select('id_cliente', DB::raw('COUNT(*) as visitas'))
                ->whereBetween('fecha', [$inicioPeriodo, $finPeriodo])
                ->groupBy('id_cliente')
                ->orderByDesc('visitas')
                ->get();

            foreach ($todosClientes as $row) {
                $cli = Cliente::find($row->id_cliente);
                $clientesLabels[] = $cli ? $cli->nombre : 'Desconocido';
                $clientesData[]   = $row->visitas;
            }
        }

        $chartClientes = new Chart();
        $chartClientes->labels($clientesLabels);
        $chartClientes->dataset(
            'Facturas por Cliente',
            'bar',
            $clientesData
        );
        $chartClientes->options([
            'responsive'            => true,
            'maintainAspectRatio'   => false,
            'scales' => [
                'yAxes' => [[ 'ticks' => [ 'beginAtZero' => true ]]]
            ]
        ]);

        // 8) Estadística: Día más productivo
        $diaMasProductivo       = null;
        $ventasDiaMasProductivo = 0.0;
        if ($inicioPeriodo && $finPeriodo) {
            $ventasPorDia = DB::table('facturacion')
                ->select(DB::raw('DATE(fecha) as dia'), DB::raw('SUM(total) as suma_ventas'))
                ->whereBetween('fecha', [$inicioPeriodo, $finPeriodo])
                ->groupBy(DB::raw('DATE(fecha)'))
                ->orderByDesc('suma_ventas')
                ->first();

            if ($ventasPorDia) {
                $diaMasProductivo       = Carbon::parse($ventasPorDia->dia)
                                            ->locale('es')
                                            ->translatedFormat('j \\d\\e F Y');
                $ventasDiaMasProductivo = (float) $ventasPorDia->suma_ventas;
            }
        }

        // 9) Estadística: Mecánico con más mano de obra
        $mecanicoMasMano      = null;
        $manoObraMecanicoMax  = 0.0;
        $mecanicosLabels      = [];
        $mecanicosData        = [];
        if ($inicioPeriodo && $finPeriodo) {
            // Top 1 Mecánico
            $topMeca = DB::table('facturacion')
                ->select('mecanico_id', DB::raw('SUM(mano_obra) as suma_mano'))
                ->whereBetween('fecha', [$inicioPeriodo, $finPeriodo])
                ->whereNotNull('mecanico_id')
                ->groupBy('mecanico_id')
                ->orderByDesc('suma_mano')
                ->first();

            if ($topMeca && $topMeca->mecanico_id) {
                $mec = Mecanico::find($topMeca->mecanico_id);
                if ($mec) {
                    $mecanicoMasMano     = $mec->nombre;
                    $manoObraMecanicoMax = (float) $topMeca->suma_mano;
                }
            }

            // Gráfico de todos los mecánicos (barras)
            $todosMeca = DB::table('facturacion')
                ->select('mecanico_id', DB::raw('SUM(mano_obra) as suma_mano'))
                ->whereBetween('fecha', [$inicioPeriodo, $finPeriodo])
                ->whereNotNull('mecanico_id')
                ->groupBy('mecanico_id')
                ->orderByDesc('suma_mano')
                ->get();

            foreach ($todosMeca as $row) {
                $mec = Mecanico::find($row->mecanico_id);
                $mecanicosLabels[] = $mec ? $mec->nombre : 'Desconocido';
                $mecanicosData[]   = $row->suma_mano;
            }
        }

        $chartMecanicos = new Chart();
        $chartMecanicos->labels($mecanicosLabels);
        $chartMecanicos->dataset(
            'Mano de Obra por Mecánico',
            'bar',
            $mecanicosData
        );
        $chartMecanicos->options([
            'responsive'            => true,
            'maintainAspectRatio'   => false,
            'scales' => [
                'yAxes' => [[ 'ticks' => [ 'beginAtZero' => true ]]]
            ]
        ]);

        // 10) Promedio de descuento
        $promedioDescuento = $facturasConDescuento
            ? round($descuentoTotal / $facturasConDescuento, 2)
            : 0.0;

        return view('estadisticas', [
            'tipo'                    => $tipo,
            'mes'                     => $mes,
            'semana'                  => $semana,
            'opMes'                   => $opMes,
            'opSem'                   => $opSem,

            // Gráficos principales
            'chartVentasYGanancias'   => $chartVentasYGanancias,
            'chartCategorias'         => $chartCategorias,

            // KPI
            'totalUnidadesVendidas'   => $totalUnidadesVendidas,
            'totalVentasPeriodo'      => round($totalVentasPeriodo, 2),
            'totalGananciasPeriodo'   => round($totalGananciasPeriodo, 2),
            'manoObraTotal'           => round($manoObraTotal, 2),
            'descuentoTotal'          => round($descuentoTotal, 2),
            'promedioDescuento'       => $promedioDescuento,
            'facturasConDescuento'    => $facturasConDescuento,

            // Cliente top
            'clienteMasVisitas'       => $clienteMasVisitas,
            'visitasClienteMax'       => $visitasClienteMax,
            'chartClientes'           => $chartClientes,

            // Día más productivo
            'diaMasProductivo'        => $diaMasProductivo,
            'ventasDiaMasProductivo'  => round($ventasDiaMasProductivo, 2),

            // Mecánico top
            'mecanicoMasMano'         => $mecanicoMasMano,
            'manoObraMecanicoMax'     => round($manoObraMecanicoMax, 2),
            'chartMecanicos'          => $chartMecanicos,
        ]);
    }
}
