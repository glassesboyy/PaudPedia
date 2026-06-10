<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapor Siswa - {{ $student['name'] }}</title>
    <style>
        @page { margin: 2cm 2.5cm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #1a1a1a; line-height: 1.5; }

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

    {{-- Assessment --}}
    <div class="section">
        <div class="section-title">Pencapaian Perkembangan</div>
        @if(count($assessments) > 0)
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No.</th>
                        <th style="width: 35%;">Aspek Perkembangan</th>
                        <th class="text-center" style="width: 12%;">Capaian</th>
                        <th style="width: 48%;">Catatan Guru</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $index => $assessment)
                        <tr>
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td>{{ $assessment['aspect'] }}</td>
                            <td class="text-center">
                                <span class="scale-badge scale-{{ strtolower($assessment['scale']) }}">
                                    {{ $assessment['scale'] }}
                                </span>
                            </td>
                            <td>{{ $assessment['notes'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="margin-top: 10px; padding: 8px; background: #f8f8f8; border: 1px solid #e5e5e5; font-size: 9px;">
                <strong>Keterangan Capaian:</strong><br>
                BB = Belum Berkembang &nbsp;|&nbsp;
                MB = Mulai Berkembang &nbsp;|&nbsp;
                BSH = Berkembang Sesuai Harapan &nbsp;|&nbsp;
                BSB = Berkembang Sangat Baik
            </div>
        @else
            <p style="text-align: center; padding: 20px; color: #999; font-style: italic;">
                Belum ada data penilaian untuk periode ini.
            </p>
        @endif
    </div>

    {{-- Signatures --}}
    <div class="signatures">
        <table>
            <tr>
                <td>
                    <p>Orang Tua / Wali,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">( ...................... )</p>
                </td>
                <td>
                    <p>Guru Kelas,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">( ...................... )</p>
                </td>
                <td>
                    <p>Kepala Sekolah,</p>
                    <div class="signature-line"></div>
                    <p class="signature-name">{{ $school['headmaster_name'] }}</p>
                </td>
            </tr>
        </table>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Dicetak pada {{ $generated_at }} oleh sistem PaudPedia SIAKAD</p>
    </div>
</body>
</html>
