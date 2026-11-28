<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard summary and analytics from live database records.
     */
    public function index(Request $request): View
    {
        $now = now();
        $todayDate = $now->toDateString();
        $yesterdayDate = $now->copy()->subDay()->toDateString();
        $tanggalHariIni = $now->copy()->locale('id')->isoFormat('dddd, D MMMM Y');

        // 1. Pendapatan 7 Hari Terakhir (termasuk hari ini & kemarin) dalam 1 query
        $startDate = $now->copy()->subDays(6)->startOfDay();
        $endDate = $now->copy()->endOfDay();

        $dailyTotals = Order::query()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('status', '!=', 'dibatalkan')
            ->selectRaw('DATE(created_at) as tanggal, SUM(COALESCE(NULLIF(total_amount, 0), total_price, 0)) as total')
            ->groupByRaw('DATE(created_at)')
            ->pluck('total', 'tanggal');

        $pendapatanHariIni = (int) ($dailyTotals[$todayDate] ?? 0);
        $pendapatanKemarin = (int) ($dailyTotals[$yesterdayDate] ?? 0);

        if ($pendapatanKemarin > 0) {
            $percentage = (int) round((($pendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100);
            $prefix = $percentage >= 0 ? '+' : '';
            $trendPendapatan = "{$prefix}{$percentage}% dari kemarin";
            $trendPendapatanType = $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'neutral');
        } elseif ($pendapatanHariIni > 0) {
            $trendPendapatan = 'Baru hari ini (kemarin Rp 0)';
            $trendPendapatanType = 'up';
        } else {
            $trendPendapatan = 'Belum ada transaksi hari ini';
            $trendPendapatanType = 'neutral';
        }

        $rawChart = [];
        for ($offset = 6; $offset >= 0; $offset--) {
            $date = $now->copy()->subDays($offset);
            $dateKey = $date->toDateString();
            $val = (int) ($dailyTotals[$dateKey] ?? 0);

            $rawChart[] = [
                'hari' => $date->copy()->locale('id')->isoFormat('ddd'),
                'tanggal' => $date->copy()->locale('id')->isoFormat('D MMM'),
                'val' => $val,
                'label' => $this->formatCompactRupiah($val),
                'full_label' => 'Rp '.number_format($val, 0, ',', '.'),
                'is_today' => $offset === 0,
            ];
        }

        $maxChartVal = max(array_column($rawChart, 'val'));
        $chart7Hari = array_map(function (array $bar) use ($maxChartVal): array {
            $bar['tinggi'] = ($bar['val'] > 0 && $maxChartVal > 0)
                ? max(8, (int) round(($bar['val'] / $maxChartVal) * 100))
                : 4;

            return $bar;
        }, $rawChart);

        // 2. Jumlah Pesanan Hari Ini & Antrean Menunggu
        $jumlahPesanan = Order::whereDate('created_at', $todayDate)
            ->where('status', '!=', 'dibatalkan')
            ->count();
        $pesananMenunggu = Order::where('status', 'menunggu')->count();

        // 3. Pengeluaran Hari Ini (aggregate query)
        $pengeluaranStats = Pengeluaran::whereDate('tanggal_pengeluaran', $todayDate)
            ->selectRaw('COALESCE(SUM(jumlah), 0) as total, COUNT(*) as transaksi')
            ->first();

        $pengeluaranHariIni = (float) ($pengeluaranStats?->total ?? 0);
        $jumlahTransaksiPengeluaran = (int) ($pengeluaranStats?->transaksi ?? 0);

        // 4. Stok Menipis
        $bahanMenipis = Bahan::whereColumn('stok', '<', 'stok_minimum')
            ->orderBy('id', 'asc')
            ->get();
        $stokMenipisCount = $bahanMenipis->count();

        // 5. Menu Terlaris (aggregasi dari order_items + orders + menus)
        $topMenuRows = OrderItem::query()
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->leftJoin('menus', 'order_items.menu_id', '=', 'menus.id')
            ->where('orders.status', '!=', 'dibatalkan')
            ->selectRaw('COALESCE(menus.name, order_items.menu_name) as nama, SUM(order_items.quantity) as terjual')
            ->groupByRaw('COALESCE(menus.name, order_items.menu_name)')
            ->orderByDesc('terjual')
            ->limit(5)
            ->get();

        $maxTerjual = (int) ($topMenuRows->max('terjual') ?? 0);
        $menuTerlaris = $topMenuRows->map(function (object $row) use ($maxTerjual): array {
            $terjual = (int) $row->terjual;

            return [
                'nama' => (string) $row->nama,
                'terjual' => $terjual,
                'persen' => $maxTerjual > 0 ? max(8, (int) round(($terjual / $maxTerjual) * 100)) : 0,
            ];
        })->all();

        // 6. Pesanan Terbaru
        $pesananTerbaru = Order::with('cafeTable')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('dashboard', [
            'tanggalHariIni' => $tanggalHariIni,
            'pendapatanHariIni' => $pendapatanHariIni,
            'trendPendapatan' => $trendPendapatan,
            'trendPendapatanType' => $trendPendapatanType,
            'jumlahPesanan' => $jumlahPesanan,
            'pesananMenunggu' => $pesananMenunggu,
            'pengeluaranHariIni' => $pengeluaranHariIni,
            'jumlahTransaksiPengeluaran' => $jumlahTransaksiPengeluaran,
            'stokMenipisCount' => $stokMenipisCount,
            'bahanMenipis' => $bahanMenipis,
            'chart7Hari' => $chart7Hari,
            'menuTerlaris' => $menuTerlaris,
            'pesananTerbaru' => $pesananTerbaru,
        ]);
    }

    /**
     * Format rupiah concisely for the 7-day chart bar labels.
     */
    private function formatCompactRupiah(int $amount): string
    {
        if ($amount >= 1000000) {
            return rtrim(rtrim(number_format($amount / 1000000, 2, '.', ''), '0'), '.').' jt';
        }

        if ($amount >= 1000) {
            return rtrim(rtrim(number_format($amount / 1000, 1, '.', ''), '0'), '.').' rb';
        }

        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
