<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapor Siswa - {{ $student['name'] }}</title>
    <style>
        @page { margin: 1.5cm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; padding: 40px; }

        /* Header */
        .header { text-align: center; border-bottom: 3px double #333; padding-bottom: 15px; margin-bottom: 20px; }
        .header-logo { width: 70px; height: 70px; margin-bottom: 8px; }
        .header h1 { font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; }
        .header h2 { font-size: 13px; font-weight: normal; margin-bottom: 2px; }
        .header p { font-size: 10px; color: #555; }

        /* Title */
        .report-title { text-align: center; margin: 20px 0; }
        .report-title h3 { font-size: 16px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; border-bottom: 2px solid #333; display: inline-block; padding-bottom: 4px; }
        .report-title p { font-size: 11px; color: #555; margin-top: 4px; }

        /* Student Info */
        .student-info { margin-bottom: 20px; }
        .student-info table { width: 100%; }
        .student-info td { padding: 3px 0; vertical-align: top; }
        .student-info .label { width: 140px; font-weight: bold; color: #333; }
        .student-info .separator { width: 15px; text-align: center; }
        .student-photo { width: 90px; height: 110px; border: 1px solid #ddd; object-fit: cover; }
        .student-photo-placeholder { width: 90px; height: 110px; border: 1px solid #ddd; background: #f5f5f5; text-align: center; line-height: 110px; font-size: 9px; color: #aaa; }

        /* Section */
        .section { margin-bottom: 25px; }
        .section-title { font-size: 13px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #999; padding-bottom: 4px; margin-bottom: 10px; color: #222; }

        /* Tables */
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .data-table th { background-color: #f0f0f0; border: 1px solid #ccc; padding: 7px 10px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
        .data-table td { border: 1px solid #ccc; padding: 6px 10px; font-size: 11px; }
        .data-table tr:nth-child(even) td { background-color: #fafafa; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }

        /* Attendance */
        .attendance-grid { display: table; width: 100%; }
        .attendance-item { display: table-cell; width: 20%; text-align: center; padding: 10px; border: 1px solid #ddd; }
        .attendance-item .number { font-size: 22px; font-weight: bold; color: #333; }
        .attendance-item .label-text { font-size: 9px; text-transform: uppercase; color: #777; letter-spacing: 0.5px; }

        /* Scale badge */
        .scale-badge { display: inline-block; padding: 2px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; }
        .scale-bb { background: #fef2f2; color: #991b1b; }
        .scale-mb { background: #fefce8; color: #854d0e; }
        .scale-bsh { background: #f0fdf4; color: #166534; }
        .scale-bsb { background: #eff6ff; color: #1e40af; }

        /* Signatures */
        .signatures { margin-top: 40px; }
        .signatures table { width: 100%; }
        .signatures td { text-align: center; padding-top: 5px; vertical-align: top; }
        .signature-line { margin-top: 60px; border-top: 1px solid #333; display: inline-block; width: 180px; }
        .signature-name { font-weight: bold; margin-top: 4px; }

        /* Footer */
        .footer { text-align: center; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; margin-top: 20px; }

        /* Notes */
        .notes-box { background: #fafafa; border: 1px solid #e0e0e0; border-radius: 3px; padding: 10px; margin-top: 5px; font-style: italic; font-size: 10px; color: #555; }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        @if($school['logo_url'])
            <img src="{{ $school['logo_url'] }}" class="header-logo" alt="Logo Sekolah">
        @endif
        <h1>{{ $school['name'] }}</h1>
        <h2>NPSN: {{ $school['npsn'] }}</h2>
        <p>{{ $school['address'] }} | {{ $school['phone'] }} | {{ $school['email'] }}</p>
    </div>

    {{-- Report Title --}}
    <div class="report-title">
        <h3>Laporan Perkembangan Peserta Didik</h3>
        <p>{{ $semester_label }} — Tahun Ajaran {{ $academic_year }}</p>
    </div>

    {{-- Student Info --}}
    <div class="student-info">
        <table>
            <tr>
                <td style="width: calc(100% - 110px);">
                    <table>
                        <tr>
                            <td class="label">Nama Lengkap</td>
                            <td class="separator">:</td>
                            <td><strong>{{ $student['name'] }}</strong></td>
                        </tr>
                        <tr>
                            <td class="label">NISN</td>
                            <td class="separator">:</td>
                            <td>{{ $student['nisn'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tanggal Lahir</td>
                            <td class="separator">:</td>
                            <td>{{ $student['birth_date'] ? \Carbon\Carbon::parse($student['birth_date'])->locale('id')->isoFormat('D MMMM Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Jenis Kelamin</td>
                            <td class="separator">:</td>
                            <td>{{ $student['gender'] === 'male' ? 'Laki-laki' : 'Perempuan' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Kelas</td>
                            <td class="separator">:</td>
                            <td>{{ $student['class_name'] }}</td>
                        </tr>
                    </table>
                </td>
                <td style="width: 110px; text-align: right;">
                    @if($student['photo_url'])
                        <img src="{{ $student['photo_url'] }}" class="student-photo" alt="Foto Siswa">
                    @else
                        <div class="student-photo-placeholder">Foto</div>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- Attendance Summary --}}
    <div class="section">
        <div class="section-title">Rekap Kehadiran</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th class="text-center">Hadir</th>
                    <th class="text-center">Sakit</th>
                    <th class="text-center">Izin</th>
                    <th class="text-center">Alfa</th>
                    <th class="text-center">Total Hari</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center"><strong>{{ $attendance['present'] }}</strong></td>
                    <td class="text-center"><strong>{{ $attendance['sick'] }}</strong></td>
                    <td class="text-center"><strong>{{ $attendance['permission'] }}</strong></td>
                    <td class="text-center"><strong>{{ $attendance['absent'] }}</strong></td>
                    <td class="text-center"><strong>{{ $attendance['total'] }}</strong></td>
                </tr>
            </tbody>
        </table>
        @if($attendance['total'] > 0)
            <p style="font-size: 10px; color: #555; text-align: right;">
                Persentase Kehadiran: <strong>{{ round(($attendance['present'] / $attendance['total']) * 100, 1) }}%</strong>
            </p>
        @endif
    </div>

    {{-- Assessment Matrix --}}
    @if(isset($programs) && count($programs) > 0)
        @php
            $monthsMap = $semester === '1' ? [
                7 => 'JUL', 8 => 'AGS', 9 => 'SEP', 10 => 'OKT', 11 => 'NOV', 12 => 'DES'
            ] : [
                1 => 'JAN', 2 => 'FEB', 3 => 'MAR', 4 => 'APR', 5 => 'MEI', 6 => 'JUN'
            ];
            $scales = ['BB' => 1, 'MB' => 2, 'BSH' => 3, 'BSB' => 4];
            $reverseScales = [1 => 'BB', 2 => 'MB', 3 => 'BSH', 4 => 'BSB'];
        @endphp
        <div class="section">
            <div class="section-title" style="margin-bottom: 10px;">Rekap Nilai (Matriks 6 Bulan)</div>
            <table class="data-table" style="font-size: 9px;">
                <thead>
                    <tr>
                        <th style="width: 45%; text-align: left;">Program & Indikator</th>
                        @foreach($monthsMap as $m)
                            <th class="text-center" style="width: 7%;">{{ $m }}</th>
                        @endforeach
                        <th class="text-center" style="width: 13%;">Capaian Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $prog)
                        <tr style="background-color: #f1f5f9;">
                            <td colspan="8" style="font-weight: bold; color: #0f172a; border-top: 1px solid #cbd5e1; border-bottom: 1px solid #cbd5e1;">{{ $prog->name }}</td>
                        </tr>
                        @foreach($prog->indicators as $ind)
                            <tr>
                                <td style="padding-left: 15px;">• {{ $ind->name }}</td>
                                @php
                                    $total = 0;
                                    $count = 0;
                                @endphp
                                @foreach($monthsMap as $num => $m)
                                    <td class="text-center" style="font-weight: bold; color: #475569;">
                                        @php
                                            $val = '-';
                                            if (isset($matrix[$ind->id])) {
                                                $monthPad = str_pad($num, 2, '0', STR_PAD_LEFT);
                                                foreach ($matrix[$ind->id] as $mKey => $mData) {
                                                    if (str_ends_with($mKey, '-' . $monthPad)) {
                                                        $val = $mData['scale'];
                                                        if (isset($scales[$val])) {
                                                            $total += $scales[$val];
                                                            $count++;
                                                        }
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ $val }}
                                    </td>
                                @endforeach
                                <td class="text-center" style="font-weight: bold; background-color: #f8fafc; border-left: 1px solid #e2e8f0;">
                                    @php
                                        $final = '-';
                                        if ($count > 0) {
                                            $avg = (int) round($total / $count);
                                            $final = $reverseScales[$avg] ?? '-';
                                        }
                                    @endphp
                                    {{ $final }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif


    {{-- Narrative Report --}}
    @if(isset($report))
        <div class="section">
            <div class="section-title">I. Pendahuluan</div>
            <p style="text-align: justify; margin-bottom: 15px; font-size: 11px;">
                {{ $report['introduction_notes'] ?: '-' }}
            </p>

            <div class="section-title">II. Perkembangan Peserta Didik</div>
            @if(count($report['details']) > 0)
                @foreach($report['details'] as $detail)
                    <div style="margin-bottom: 12px;">
                        <h4 style="font-size: 12px; margin-bottom: 4px;">{{ $detail['program'] }}</h4>
                        <p style="text-align: justify; font-size: 11px; margin-left: 10px;">
                            {{ $detail['narrative'] ?: '-' }}
                        </p>
                    </div>
                @endforeach
            @else
                <p style="color: #999; font-style: italic;">Belum ada catatan per program perkembangan.</p>
            @endif

            <div class="section-title" style="margin-top: 15px;">III. Penutup</div>
            <p style="text-align: justify; margin-bottom: 15px; font-size: 11px;">
                {{ $report['closing_notes'] ?: '-' }}
            </p>

            <div class="section-title">IV. Rekomendasi / Saran</div>
            <p style="text-align: justify; margin-bottom: 15px; font-size: 11px;">
                {{ $report['recommendation'] ?: '-' }}
            </p>
        </div>


    @else
        <div class="section">
            <p style="text-align: center; padding: 20px; color: #999; font-style: italic;">
                Belum ada data rapor naratif untuk periode ini.
            </p>
        </div>
    @endif



    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada {{ $generated_at }} oleh sistem PaudPedia SIAKAD</p>
    </div>
</body>
</html>
