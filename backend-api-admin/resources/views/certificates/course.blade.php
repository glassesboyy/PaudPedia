<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kursus</title>
    <style>
        @page {
            margin: 12mm;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #1e293b;
            background: #ffffff;
        }

        .frame {
            border: 3px solid #b8860b;
            padding: 4mm;
            page-break-inside: avoid;
        }

        .inner {
            border: 1px solid #d4a847;
            padding: 10mm 16mm;
            text-align: center;
        }

        .brand {
            font-size: 11px;
            font-weight: 700;
            color: #b8860b;
            letter-spacing: 4px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a2e;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }

        .line {
            width: 100px;
            border-top: 2px solid #b8860b;
            margin: 7px auto 9px;
        }

        .sub {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 9px;
        }

        .name {
            font-size: 24px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 2px;
        }

        .name-line {
            width: 240px;
            border-top: 1px solid #cbd5e1;
            margin: 0 auto 9px;
        }

        .clabel {
            font-size: 10px;
            color: #64748b;
            margin-bottom: 4px;
        }

        .ctitle {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 12px;
        }

        /* BADGE FIX */
        .badge {
            width: 70px;
            height: 70px;
            margin: 12px auto;
            display: block;
        }

        .badge svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        .note {
            font-size: 9px;
            color: #64748b;
            margin-bottom: 10px;
        }

        .meta {
            width: 100%;
        }

        .meta td {
            font-size: 8px;
            color: #64748b;
            line-height: 1.5;
            padding: 0 10px;
        }

        .ml { text-align: left; }
        .mr { text-align: right; }

        .mlabel {
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 7px;
        }

        .mval {
            color: #334155;
            font-size: 9px;
        }
    </style>
</head>
<body>
<div class="frame">
    <div class="inner">
        <div class="brand">PaudPedia</div>
        <div class="title">Sertifikat Kelulusan</div>
        <div class="line"></div>

        <div class="sub">Dengan ini menyatakan bahwa</div>

        <div class="name">{{ $user_name }}</div>
        <div class="name-line"></div>

        <div class="clabel">telah berhasil menyelesaikan kursus</div>
        <div class="ctitle">&ldquo;{{ $course_title }}&rdquo;</div>

        <!-- SVG BADGE (FIXED) -->
        <div class="badge">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100">
                <circle cx="50" cy="50" r="45"
                        stroke="#b8860b"
                        stroke-width="3"
                        fill="none"/>

                <circle cx="50" cy="50" r="35"
                        stroke="#d4a847"
                        stroke-width="1"
                        fill="none"/>

                <path d="M35 52 L47 64 L70 38"
                      fill="none"
                      stroke="#b8860b"
                      stroke-width="4"
                      stroke-linecap="round"
                      stroke-linejoin="round"/>
            </svg>
        </div>

        <div class="note">dengan progres 100% pada platform pembelajaran PaudPedia</div>

        <table class="meta">
            <tr>
                <td class="ml">
                    <span class="mlabel">Nomor Sertifikat</span><br>
                    <span class="mval">{{ $certificate_number }}</span>
                </td>
                <td class="mr">
                    <span class="mlabel">Tanggal Terbit</span><br>
                    <span class="mval">{{ $issue_date }}</span>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>