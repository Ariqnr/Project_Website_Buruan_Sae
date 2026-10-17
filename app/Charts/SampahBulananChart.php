<?php

namespace App\Charts;

use App\Models\Sampah;
use ArielMejiaDev\LarapexCharts\BarChart;
use ArielMejiaDev\LarapexCharts\LarapexChart;
use Carbon\Carbon;

class SampahBulananChart
{
    /**
     * Membangun grafik batang total berat sampah selama 6 bulan terakhir.
     */
    public function build(): BarChart
    {
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->startOfMonth()->subMonths($i);
            $year = $date->year;
            $month = $date->month;

            $totalBerat = (float) Sampah::whereYear('tanggal_masuk', $year)
                ->whereMonth('tanggal_masuk', $month)
                ->sum('berat_kg');

            $labels[] = $date->translatedFormat('M Y');
            $data[] = round($totalBerat, 2);
        }

        return (new LarapexChart)->barChart()
            ->setTitle('Total Berat Sampah Masuk')
            ->setSubtitle('Perkembangan data 6 bulan terakhir (dalam kilogram)')
            ->addData($data, 'Berat (kg)')
            ->setXAxis($labels)
            ->setColors(['#15803d'])
            ->setHeight(320);
    }
}
