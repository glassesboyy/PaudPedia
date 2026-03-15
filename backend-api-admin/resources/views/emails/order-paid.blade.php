<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f9; font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);">
                    {{-- Header --}}
                    <tr>
                        <td style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding:32px 40px; text-align:center;">
                            <h1 style="margin:0; color:#ffffff; font-size:24px; font-weight:700;">
                                Pembayaran Berhasil!
                            </h1>
                            <p style="margin:8px 0 0; color:#dbeafe; font-size:14px;">
                                {{ $order->order_number }}
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding:32px 40px;">
                            <p style="margin:0 0 16px; color:#374151; font-size:16px; line-height:1.6;">
                                Halo <strong>{{ $userName }}</strong>,
                            </p>
                            <p style="margin:0 0 24px; color:#6b7280; font-size:14px; line-height:1.6;">
                                Terima kasih! Pembayaran Anda telah berhasil diproses. Berikut detail pesanan Anda:
                            </p>

                            {{-- Items --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                                <tr>
                                    <td style="padding:10px 12px; background-color:#f9fafb; border-bottom:2px solid #e5e7eb; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase;">
                                        Item
                                    </td>
                                    <td style="padding:10px 12px; background-color:#f9fafb; border-bottom:2px solid #e5e7eb; font-size:12px; font-weight:600; color:#6b7280; text-transform:uppercase; text-align:right;">
                                        Subtotal
                                    </td>
                                </tr>
                                @foreach ($order->items as $item)
                                <tr>
                                    <td style="padding:12px; border-bottom:1px solid #f3f4f6; color:#374151; font-size:14px;">
                                        <strong>{{ $item->item_title }}</strong>
                                        <br>
                                        <span style="color:#9ca3af; font-size:12px;">{{ $item->item_type->label() }}</span>
                                        @if ($item->quantity > 1)
                                            <span style="color:#9ca3af; font-size:12px;">&times; {{ $item->quantity }}</span>
                                        @endif
                                    </td>
                                    <td style="padding:12px; border-bottom:1px solid #f3f4f6; color:#374151; font-size:14px; text-align:right; white-space:nowrap;">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach

                                {{-- Summary --}}
                                <tr>
                                    <td style="padding:10px 12px; color:#6b7280; font-size:14px;">Subtotal</td>
                                    <td style="padding:10px 12px; color:#374151; font-size:14px; text-align:right;">
                                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @if ($order->discount_amount > 0)
                                <tr>
                                    <td style="padding:6px 12px; color:#059669; font-size:14px;">
                                        Diskon
                                        @if ($order->promo_code)
                                            ({{ $order->promo_code }})
                                        @endif
                                    </td>
                                    <td style="padding:6px 12px; color:#059669; font-size:14px; text-align:right;">
                                        -Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px; border-top:2px solid #e5e7eb; color:#111827; font-size:16px; font-weight:700;">
                                        Total
                                    </td>
                                    <td style="padding:12px; border-top:2px solid #e5e7eb; color:#2563eb; font-size:16px; font-weight:700; text-align:right;">
                                        Rp {{ number_format($order->final_amount, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>

                            {{-- Access Info --}}
                            <div style="background-color:#eff6ff; border-radius:8px; padding:20px; margin-bottom:24px;">
                                <p style="margin:0 0 12px; color:#1e40af; font-size:14px; font-weight:600;">
                                    Akses Produk Anda:
                                </p>
                                <ul style="margin:0; padding:0 0 0 20px; color:#374151; font-size:13px; line-height:1.8;">
                                    @foreach ($order->items as $item)
                                        @if ($item->item_type->value === 'course')
                                            <li><strong>{{ $item->item_title }}</strong> — Akses kursus di halaman <a href="{{ $frontendUrl }}/account/courses" style="color:#2563eb;">Kursus Saya</a></li>
                                        @elseif ($item->item_type->value === 'webinar')
                                            <li><strong>{{ $item->item_title }}</strong> — Lihat detail & link Zoom di <a href="{{ $frontendUrl }}/account/webinars" style="color:#2563eb;">Webinar Saya</a></li>
                                        @elseif ($item->item_type->value === 'product')
                                            <li><strong>{{ $item->item_title }}</strong> — Download di <a href="{{ $frontendUrl }}/account/products" style="color:#2563eb;">Produk Saya</a></li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>

                            {{-- CTA --}}
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <a href="{{ $frontendUrl }}/account/orders"
                                           style="display:inline-block; padding:14px 32px; background-color:#2563eb; color:#ffffff; text-decoration:none; border-radius:8px; font-size:14px; font-weight:600;">
                                            Lihat Pesanan Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding:24px 40px; background-color:#f9fafb; border-top:1px solid #e5e7eb; text-align:center;">
                            <p style="margin:0; color:#9ca3af; font-size:12px;">
                                &copy; {{ date('Y') }} {{ config('app.name', 'PaudPedia') }}. Semua hak dilindungi.
                            </p>
                            <p style="margin:8px 0 0; color:#d1d5db; font-size:11px;">
                                Email ini dikirim secara otomatis. Mohon tidak membalas email ini.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
