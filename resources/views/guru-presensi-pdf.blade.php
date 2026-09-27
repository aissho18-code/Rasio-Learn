<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekapitulasi Presensi Siswa - Ratio Learn</title>

    <!-- Google Font: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body { 
            font-family: 'Poppins', sans-serif; 
            font-size: 12px; 
            color: #1e293b; 
            background: #ffffff; 
            margin: 0; 
            padding: 30px; 
            -webkit-font-smoothing: antialiased;
        }
        .header { 
            text-align: center; 
            margin-bottom: 25px; 
            border-bottom: 2px solid #0F1A34; 
            padding-bottom: 15px; 
        }
        .header h2 { 
            font-size: 18px; 
            font-weight: 700; 
            color: #0F1A34; 
            margin: 0 0 5px 0; 
            letter-spacing: 0.025em;
        }
        .header p { 
            font-size: 12px; 
            color: #64748b; 
            margin: 0; 
            font-weight: 500;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 15px; 
            font-size: 11px; 
        }
        th, td { 
            border: 1px solid #cbd5e1; 
            padding: 10px 12px; 
            text-align: left; 
        }
        th { 
            background-color: #f1f5f9; 
            font-weight: 600; 
            color: #334155; 
            text-transform: uppercase; 
            font-size: 10px; 
            letter-spacing: 0.05em; 
        }
        tr:nth-child(even) { 
            background-color: #f8fafc; 
        }
        .badge { 
            padding: 4px 10px; 
            border-radius: 9999px; 
            font-weight: 700; 
            font-size: 9px; 
            text-transform: uppercase; 
            display: inline-block; 
            letter-spacing: 0.05em;
        }
        .badge-hadir { background-color: #dcfce7; color: #15803d; }
        .badge-izin { background-color: #dbeafe; color: #1d4ed8; }
        .badge-sakit { background-color: #fef3c7; color: #b45309; }
        .badge-alfa { background-color: #fee2e2; color: #b91c1c; }
        
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #64748b;
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h2>LAPORAN REKAPITULASI KEHADIRAN SISWA</h2>
        <p>Mata Pelajaran: Matematika &bull; Platform Edukasi Ratio Learn</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                <th style="width: 25%;">Tanggal / Waktu</th>
                <th style="width: 30%;">Nama Siswa</th>
                <th style="width: 15%; text-align: center;">Status</th>
                <th style="width: 25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($absensis ?? [] as $index => $absen)
                <tr>
                    <td style="text-align: center; font-weight: 600; color: #64748b;">{{ $index + 1 }}</td>
                    <td>{{ $absen->created_at ? $absen->created_at->format('d/m/Y H:i') : '-' }}</td>
                    <td style="font-weight: 600; color: #1e293b;">{{ $absen->siswa->name ?? '-' }}</td>
                    <td style="text-align: center;">
                        @php
                            $status = strtolower($absen->status ?? 'hadir');
                        @endphp
                        <span class="badge badge-{{ $status }}">
                            {{ strtoupper($absen->status) }}
                        </span>
                    </td>
                    <td>{{ $absen->keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #94a3b8; font-style: italic; padding: 20px;">
                        Belum ada data presensi siswa yang tercatat.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <div>Dicetak otomatis oleh Sistem Ratio Learn</div>
        <div>Tanggal Cetak: {{ date('d/m/Y H:i') }}</div>
    </div>

</body>
</html>