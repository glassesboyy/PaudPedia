<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat Kursus</title>
    <style>
        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            background: #ffffff;
        }

        .page {
            width: 100%;
            height: 100%;
            padding: 44px;
            box-sizing: border-box;
        }

        .frame {
            width: 100%;
            height: 100%;
            border: 6px solid #0ea5e9;
            border-radius: 14px;
            padding: 40px;
            box-sizing: border-box;
            text-align: center;
            background: #f8fbff;
        }

        .brand {
            font-size: 20px;
            font-weight: 700;
            color: #0284c7;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .title {
            font-size: 38px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #0f172a;
        }

        .subtitle {
            font-size: 16px;
            margin-bottom: 24px;
            color: #475569;
        }

        .name {
            font-size: 32px;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 16px;
        }

        .course {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 26px;
        }

        .meta {
            font-size: 13px;
            color: #334155;
            margin-top: 30px;
            line-height: 1.8;
        }
    </style>
</head>
<body>
<div class="page">
    <div class="frame">
        <div class="brand">PaudPedia LMS</div>
        <div class="title">Sertifikat Kelulusan</div>
        <div class="subtitle">Diberikan kepada peserta yang telah menyelesaikan kursus dengan progres 100%</div>

        <div class="name">{{ $user_name }}</div>
        <div class="subtitle">atas keberhasilan menyelesaikan kursus</div>
        <div class="course">{{ $course_title }}</div>

        <div class="meta">
            Nomor Sertifikat: {{ $certificate_number }}<br>
            Tanggal Terbit: {{ $issue_date }}
        </div>
    </div>
</div>
</body>
</html>
