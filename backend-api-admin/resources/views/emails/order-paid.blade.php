<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - PaudPedia</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; line-height: 1.5; color: #334155;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8fafc; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px; text-align: center; border-bottom: 1px solid #e2e8f0;">
                            <!-- App Logo/Name -->
                            <h1 style="margin: 0 0 12px; font-size: 24px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px;">
                                Paud<span style="color: #2563eb;">Pedia</span>
                            </h1>
                            <div style="width: 48px; height: 48px; background-color: #dbeafe; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                                <!-- Checkmark Vector -->
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block; margin: 12px auto;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <h2 style="margin: 0 0 8px; font-size: 20px; font-weight: 700; color: #1e293b;">Pembayaran Berhasil</h2>
                            <p style="margin: 0; font-size: 14px; color: #64748b;">ID Pesanan: <strong>{{ $order->order_number }}</strong></p>
                        </td>
                    </tr>

                    <!-- Customer Greeting -->
                    <tr>
                        <td style="padding: 32px 40px 16px;">
                            <p style="margin: 0 0 8px; font-size: 15px; color: #334155;">Halo, <strong>{{ $userName }}</strong></p>
                            <p style="margin: 0; font-size: 14px; color: #475569;">Terima kasih telah berbelanja di PaudPedia. Pembayaran Anda telah kami terima dan pesanan Anda kini sudah bisa diakses secara instan.</p>
                        </td>
                    </tr>

                    <!-- Order Summary Table -->
                    <tr>
                        <td style="padding: 16px 40px;">
                            <table width="100%" cellpadding="0" cellspacing="0" style="border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                                <tr>
                                    <th style="padding: 12px 16px; background-color: #f8fafc; text-align: left; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">Item</th>
                                    <th style="padding: 12px 16px; background-color: #f8fafc; text-align: right; font-size: 12px; font-weight: 600; color: #64748b; text-transform: uppercase; border-bottom: 1px solid #e2e8f0;">Harga</th>
                                </tr>
                                @foreach ($order->items as $item)
                                <tr>
                                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9;">
                                        <p style="margin: 0 0 4px; font-size: 14px; font-weight: 600; color: #1e293b;">{{ $item->item_title }}</p>
                                        <p style="margin: 0; font-size: 12px; color: #64748b;">
                                            {{ $item->item_type instanceof \App\Enums\OrderItemType ? $item->item_type->label() : $item->item_type }}
                                        </p>
                                    </td>
                                    <td style="padding: 16px; border-bottom: 1px solid #f1f5f9; text-align: right; font-size: 14px; font-weight: 500; color: #334155;">
                                        Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                                <!-- Totals -->
                                <tr>
                                    <td style="padding: 12px 16px; text-align: right; font-size: 13px; color: #64748b;">Subtotal</td>
                                    <td style="padding: 12px 16px; text-align: right; font-size: 13px; font-weight: 500; color: #334155;">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                                </tr>
                                @if ($order->discount_amount > 0)
                                <tr>
                                    <td style="padding: 4px 16px 12px; text-align: right; font-size: 13px; color: #059669;">Diskon {{ $order->promo_code ? '('.$order->promo_code.')' : '' }}</td>
                                    <td style="padding: 4px 16px 12px; text-align: right; font-size: 13px; font-weight: 500; color: #059669;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 16px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right; font-size: 14px; font-weight: 700; color: #0f172a;">Total Pembayaran</td>
                                    <td style="padding: 16px; background-color: #f8fafc; border-top: 1px solid #e2e8f0; text-align: right; font-size: 16px; font-weight: 700; color: #2563eb;">Rp {{ number_format($order->final_amount, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Access Items Section -->
                    <tr>
                        <td style="padding: 24px 40px 32px;">
                            <h3 style="margin: 0 0 16px; font-size: 16px; font-weight: 700; color: #1e293b;">Panduan Akses</h3>
                            
                            @foreach ($order->items as $item)
                                @php
                                    $itemModel = $item->item;
                                    $type = $item->item_type instanceof \App\Enums\OrderItemType ? $item->item_type->value : $item->item_type;
                                @endphp

                                @if ($type === 'course' && $itemModel)
                                    <!-- Course Item -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #3b82f6;">
                                        <tr>
                                            <td style="padding: 20px;">
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td width="32" valign="top" style="color: #3b82f6;">
                                                            <!-- File Icon -->
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20"/></svg>
                                                        </td>
                                                        <td>
                                                            <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Kursus</p>
                                                            <p style="margin: 0 0 12px; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $item->item_title }}</p>
                                                            <a href="{{ $frontendUrl }}/learn/{{ $itemModel->slug }}" style="display: inline-block; background-color: #eff6ff; color: #2563eb; font-size: 13px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 6px;">Mulai Belajar</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @elseif ($type === 'webinar' && $itemModel)
                                    <!-- Webinar Item -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #8b5cf6;">
                                        <tr>
                                            <td style="padding: 20px;">
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td width="32" valign="top" style="color: #8b5cf6;">
                                                            <!-- Camera Icon -->
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"></polygon><rect x="1" y="5" width="15" height="14" rx="2" ry="2"></rect></svg>
                                                        </td>
                                                        <td>
                                                            <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Webinar</p>
                                                            <p style="margin: 0 0 8px; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $item->item_title }}</p>
                                                            
                                                            @if ($itemModel->event_date)
                                                                <p style="margin: 0 0 12px; font-size: 13px; color: #475569;">
                                                                    Tanggal: {{ \Carbon\Carbon::parse($itemModel->event_date)->translatedFormat('l, d F Y \\p\\u\\k\\u\\l H:i') }} WIB
                                                                </p>
                                                            @endif
            
                                                            @if ($itemModel->zoom_link)
                                                                <div style="margin-bottom: 12px;">
                                                                    <a href="{{ $itemModel->zoom_link }}" style="display: inline-block; background-color: #8b5cf6; color: #ffffff; font-size: 13px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 6px;">Link Zoom Meeting</a>
                                                                </div>
                                                            @else
                                                                <p style="margin: 0 0 12px; font-size: 12px; color: #64748b;">Link Zoom dapat diakses selengkapnya di <a href="{{ $frontendUrl }}/account/webinars" style="color: #8b5cf6; text-decoration: none; font-weight: 600;">Menu Webinar Saya</a></p>
                                                            @endif
            
                                                            @if ($itemModel->zoom_meeting_id || $itemModel->zoom_passcode)
                                                                <div style="background-color: #f8fafc; padding: 10px 14px; border-radius: 6px; font-size: 13px; color: #475569; border: 1px solid #e2e8f0;">
                                                                    @if ($itemModel->zoom_meeting_id) <strong style="color: #1e293b;">Meeting ID:</strong> {{ $itemModel->zoom_meeting_id }}<br> @endif
                                                                    @if ($itemModel->zoom_passcode) <strong style="color: #1e293b;">Passcode:</strong> {{ $itemModel->zoom_passcode }} @endif
                                                                </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @elseif ($type === 'product' && $itemModel)
                                    <!-- Product Item -->
                                    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; margin-bottom: 16px; border-left: 4px solid #10b981;">
                                        <tr>
                                            <td style="padding: 20px;">
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td width="32" valign="top" style="color: #10b981;">
                                                            <!-- Box Icon -->
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                                                        </td>
                                                        <td>
                                                            <p style="margin: 0 0 4px; font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px;">Produk Digital</p>
                                                            <p style="margin: 0 0 12px; font-size: 15px; font-weight: 700; color: #0f172a;">{{ $item->item_title }}</p>
                                                            <a href="{{ $frontendUrl }}/account/products" style="display: inline-block; background-color: #10b981; color: #ffffff; font-size: 13px; font-weight: 600; text-decoration: none; padding: 8px 16px; border-radius: 6px;">Unduh File Sekarang</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @endif
                            @endforeach

                            <!-- Global CTA -->
                            <div style="text-align: center; margin-top: 32px;">
                                <a href="{{ $frontendUrl }}/account/orders" style="display: inline-block; background-color: #0f172a; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none; padding: 12px 32px; border-radius: 8px;">Lihat Histori Transaksi</a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px; background-color: #f1f5f9; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 8px; font-size: 13px; color: #64748b; font-weight: 500;">Tim Support PaudPedia</p>
                            <p style="margin: 0 0 16px; font-size: 12px; color: #94a3b8;">Tidak membuat pesanan ini? Cukup abaikan email ini.</p>
                            <div style="font-size: 11px; color: #cbd5e1; border-top: 1px solid #e2e8f0; padding-top: 16px; margin-top: 8px;">
                                &copy; {{ date('Y') }} PaudPedia. Hak cipta dilindungi undang-undang.
                            </div>
                        </td>
                    </tr>
                </table>
                <div style="margin-top: 20px; text-align: center; font-size: 11px; color: #94a3b8;">
                    Email tanda terima otomatis. Dokumen sistem resmi tanpa perlu balas tangan.
                </div>
            </td>
        </tr>
    </table>
</body>
</html>
