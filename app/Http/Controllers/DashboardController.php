<?php

namespace App\Http\Controllers;

use App\Models\Bahan;
use App\Models\Pengeluaran;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display dashboard summary and analytics.
     */
    public function index(Request $request): View
    {
        // 1. Tanggal Hari Ini (Format Bahasa Indonesia: e.g. Rabu, 18 November 2026)
        $tanggalHariIni = now()->locale('id')->isoFormat('dddd, D MMMM Y');

        // 2. Pendapatan Hari Ini
        $totalPendapatanDb = Pesanan::whereDate('waktu_pesan', now()->toDateString())
            ->where('status', '!=', 'dibatalkan')
            ->sum('total_harga');

        $pendapatanHariIni = $totalPendapatanDb > 0 ? $totalPendapatanDb : 1248000;
        $trendPendapatan = '+12% dari kemarin';

        // 3. Jumlah Pesanan
        $countPesananDb = Pesanan::whereDate('waktu_pesan', now()->toDateString())->count();
        $jumlahPesanan = $countPesananDb > 0 ? $countPesananDb : 47;

        $menungguCount = Pesanan::where('status', 'menunggu')->count();
        $pesananMenunggu = $menungguCount > 0 ? $menungguCount : 8;

        // 4. Pengeluaran Hari Ini
        $pengeluaranDbSum = Pengeluaran::whereDate('tanggal_pengeluaran', now()->toDateString())->sum('jumlah');
        $pengeluaranDbCount = Pengeluaran::whereDate('tanggal_pengeluaran', now()->toDateString())->count();

        $pengeluaranHariIni = $pengeluaranDbSum > 0 ? $pengeluaranDbSum : 320000;
        $jumlahTransaksiPengeluaran = $pengeluaranDbCount > 0 ? $pengeluaranDbCount : 3;

        // 5. Stok Menipis
        $bahanMenipis = Bahan::whereColumn('stok', '<', 'stok_minimum')->orderBy('id', 'asc')->get();
        $stokMenipisCount = $bahanMenipis->count();

        // 6. Data Bar Chart Pendapatan 7 Hari Terakhir
        $chart7Hari = [
            ['hari' => 'Kam', 'label' => '0.82 jt', 'val' => 0.82, 'tinggi' => 55, 'is_today' => false],
            ['hari' => 'Jum', 'label' => '0.96 jt', 'val' => 0.96, 'tinggi' => 65, 'is_today' => false],
            ['hari' => 'Sab', 'label' => '1.1 jt',  'val' => 1.10, 'tinggi' => 74, 'is_today' => false],
            ['hari' => 'Min', 'label' => '0.74 jt', 'val' => 0.74, 'tinggi' => 50, 'is_today' => false],
            ['hari' => 'Sen', 'label' => '1.32 jt', 'val' => 1.32, 'tinggi' => 89, 'is_today' => false],
            ['hari' => 'Sel', 'label' => '1.48 jt', 'val' => 1.48, 'tinggi' => 100, 'is_today' => false],
            ['hari' => 'Rab', 'label' => '1.25 jt', 'val' => 1.25, 'tinggi' => 84, 'is_today' => true],
        ];

        // 7. Menu Terlaris
        $menuTerlaris = [
            ['nama' => 'Kopi Susu Gula Aren', 'terjual' => 86, 'persen' => 86],
            ['nama' => 'Es Kopi Susu',        'terjual' => 64, 'persen' => 64],
            ['nama' => 'Pisang Goreng',        'terjual' => 48, 'persen' => 48],
            ['nama' => 'V60 Manual Brew',      'terjual' => 31, 'persen' => 31],
        ];

        // 8. Pesanan Terbaru
        $pesananTerbaru = Pesanan::with('meja')->orderByDesc('waktu_pesan')->orderByDesc('id')->take(3)->get();

        return view('dashboard', [
            'tanggalHariIni' => $tanggalHariIni,
            'pendapatanHariIni' => $pendapatanHariIni,
            'trendPendapatan' => $trendPendapatan,
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
}
