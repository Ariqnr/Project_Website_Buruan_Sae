<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Pengelolaan Sampah Buruan Sae ({{ $tanggalAwal }} s/d {{ $tanggalAkhir }})</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 18mm 15mm;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.45;
            color: #1f2937;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }
        .header {
            border-bottom: 2px solid #15803d;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            color: #14532d;
            margin: 0 0 4px 0;
        }
        .header p {
            margin: 2px 0;
            font-size: 11px;
            color: #374151;
        }
        .meta-table {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 2px 0;
            font-size: 11px;
            color: #374151;
        }
        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #14532d;
            margin: 16px 0 8px 0;
            padding-bottom: 4px;
            border-bottom: 1px solid #d1d5db;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.data-table th {
            background-color: #f3f4f6;
            color: #111827;
            font-weight: bold;
            text-align: left;
            padding: 7px 8px;
            border: 1px solid #d1d5db;
            font-size: 10.5px;
            text-transform: uppercase;
        }
        table.data-table td {
            padding: 6px 8px;
            border: 1px solid #d1d5db;
            font-size: 11px;
            vertical-align: top;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .summary-box {
            background-color: #f9fafb;
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            margin-bottom: 16px;
        }
        .summary-total {
            font-size: 13px;
            font-weight: bold;
            color: #14532d;
            margin-bottom: 8px;
        }
        .empty-text {
            padding: 12px;
            text-align: center;
            color: #6b7280;
            font-style: italic;
            border: 1px solid #e5e7eb;
            margin-bottom: 12px;
        }
    </style>
</head>
<body>
    {{-- Kop Laporan --}}
    <div class="header">
        <h1>Laporan Pengelolaan Sampah Buruan Sae</h1>
        <table class="meta-table">
            <tr>
                <td style="width: 130px;"><strong>Periode Laporan</strong></td>
                <td>: {{ $periodeAwalLabel }} s/d {{ $periodeAkhirLabel }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Cetak</strong></td>
                <td>: {{ $tanggalCetak }}</td>
            </tr>
            <tr>
                <td><strong>Dicetak Oleh</strong></td>
                <td>: {{ $dicetakOleh }}</td>
            </tr>
        </table>
    </div>

    {{-- Bagian 1: Tabel Data Sampah --}}
    <div class="section-title">1. Data Pencatatan Sampah Masuk</div>
    @if ($daftarSampah->isEmpty())
        <div class="empty-text">Tidak ada data sampah yang tercatat pada periode ini.</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 18%;">Tanggal Masuk</th>
                    <th style="width: 24%;">Jenis</th>
                    <th style="width: 18%;" class="text-right">Berat (kg)</th>
                    <th style="width: 16%;">Status</th>
                    <th style="width: 24%;">Diinput Oleh</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($daftarSampah as $item)
                    <tr>
                        <td>{{ $item->tanggal_masuk->translatedFormat('d M Y') }}</td>
                        <td><strong>{{ $item->jenis }}</strong></td>
                        <td class="text-right">{{ number_format($item->berat_kg, 2, ',', '.') }} kg</td>
                        <td>{{ $item->status_label }}</td>
                        <td>{{ $item->user->name ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Ringkasan Total Berat --}}
    <div class="summary-box">
        <div class="summary-total">
            Total Berat Seluruhnya: {{ number_format($totalBerat, 2, ',', '.') }} kg
        </div>
        <div>
            <strong>Rincian Total Berat per Jenis Sampah:</strong>
            @if ($rekapPerJenis->isEmpty())
                <span>-</span>
            @else
                <table class="data-table" style="margin-top: 6px; margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th>Jenis Sampah</th>
                            <th class="text-right">Total Berat (kg)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rekapPerJenis as $jenis => $berat)
                            <tr>
                                <td>{{ $jenis }}</td>
                                <td class="text-right">{{ number_format($berat, 2, ',', '.') }} kg</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- Bagian 2: Jadwal Penjemuran pada Periode Ini --}}
    <div class="section-title">2. Jadwal Penjemuran pada Periode Ini</div>
    @if ($jadwalPeriode->isEmpty())
        <div class="empty-text">Tidak ada jadwal penjemuran yang berlangsung pada periode ini.</div>
    @else
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 34%;">Jenis</th>
                    <th style="width: 22%;">Tanggal Mulai</th>
                    <th style="width: 22%;">Tanggal Selesai</th>
                    <th style="width: 22%;">Status Jadwal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($jadwalPeriode as $jadwal)
                    <tr>
                        <td>
                            <strong>{{ $jadwal->sampah->jenis ?? '-' }}</strong>
                            ({{ number_format($jadwal->sampah->berat_kg ?? 0, 2, ',', '.') }} kg)
                        </td>
                        <td>{{ $jadwal->tanggal_mulai->translatedFormat('d M Y') }}</td>
                        <td>{{ $jadwal->tanggal_selesai->translatedFormat('d M Y') }}</td>
                        <td>{{ $jadwal->status_label }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
