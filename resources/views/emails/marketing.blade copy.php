<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Your iPhone 17 Pro Max</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <style>
        /* Basic email-safe inline CSS */
        body {
            margin: 0;
            padding: 0;
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Arial, sans-serif;
            color: #111827;
        }
        .wrapper {
            width: 100%;
            background-color: #f3f4f6;
            padding: 24px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
        }
        .inner {
            padding: 24px 24px 18px 24px;
        }
        .h1 {
            font-size: 22px;
            font-weight: 600;
            margin: 0 0 4px;
        }
        .subtext {
            font-size: 13px;
            color: #6b7280;
            margin: 0 0 16px;
        }
        .product-card {
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            margin-bottom: 18px;
        }
        .product-image {
            display: block;
            width: 100%;
            border-bottom: 1px solid #e5e7eb;
        }
        .product-body {
            padding: 14px 16px 16px;
        }
        .product-name {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 4px;
        }
        .product-price {
            font-size: 16px;
            font-weight: 600;
            color: #16a34a;
            margin: 0 0 10px;
        }
        .product-meta {
            font-size: 12px;
            color: #4b5563;
            margin: 0 0 2px;
        }
        .tracking-section-title {
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 8px;
        }
        .tracking-steps {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }
        .tracking-steps td {
            padding: 6px 4px;
            font-size: 12px;
            color: #4b5563;
            white-space: nowrap;
        }
        .tracking-steps .step-label {
            font-weight: 500;
        }
        .tracking-steps .step-dot {
            width: 10px;
        }
        .dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background-color: #e5e7eb;
        }
        .dot-active {
            background-color: #2563eb;
        }
        .step-active {
            color: #111827;
        }
        .btn {
            display: inline-block;
            padding: 9px 18px;
            border-radius: 999px;
            background-color: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .footer {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 16px;
            line-height: 1.5;
        }
        .footer a {
            color: #6b7280;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td align="center">
                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width:600px;">
                        <tr>
                            <td style="padding:0 16px;">
                                <div class="container">
                                    <div class="inner">
                                        <p class="subtext" style="margin-bottom: 6px;">
                                            {{ config('app.name') }}
                                        </p>
                                        <h1 class="h1">
                                            @if(!empty($user->name))
                                                Hi Wazhma,
                                            @else
                                                Hi,
                                            @endif
                                        </h1>
                                        <p class="subtext">
                                            Your iPhone 17 Pro Max is on the way. Please Confirm the DHL Package
                                        </p>

                                        {{-- Product card --}}
                                        <div class="product-card">
                                            @if(!empty($iphoneImageUrl))
                                                <img src="{{ $iphoneImageUrl }}"
                                                     alt="iPhone 17 Pro Max"
                                                     class="product-image">
                                            @endif

                                            <div class="product-body">
                                                <p class="product-name">
                                                    iPhone 17 Pro Max – 256 GB, Titanium
                                                </p>
                                                <p class="product-price">
                                                    {{ $price ?? '1.599,00 €' }}
                                                </p>

                                                <p class="product-meta">
                                                    Order number: <strong>{{ $orderNumber ?? 'AB-123456' }}</strong>
                                                </p>
                                                <p class="product-meta">
                                                    Tracking code: <strong>{{ $trackingCode ?? 'DE1234567890' }}</strong>
                                                </p>
                                                <p class="product-meta">
                                                    Current status: <strong>On the way</strong>
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Tracking steps --}}
                                        @php
                                            $status = $status ?? 'on_the_way';
                                        @endphp

                                        <p class="tracking-section-title">Tracking status</p>
                                        <table class="tracking-steps" role="presentation">
                                            <tr>
                                                <td class="step-dot">
                                                    <span class="dot dot-active"></span>
                                                </td>
                                                <td class="step-label step-active">Order confirmed</td>
                                            </tr>
                                            <tr>
                                                <td class="step-dot">
                                                    <span class="dot {{ in_array($status, ['packed','shipped','on_the_way','delivered']) ? 'dot-active' : '' }}"></span>
                                                </td>
                                                <td class="step-label {{ in_array($status, ['packed','shipped','on_the_way','delivered']) ? 'step-active' : '' }}">
                                                    Packed
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="step-dot">
                                                    <span class="dot {{ in_array($status, ['shipped','on_the_way','delivered']) ? 'dot-active' : '' }}"></span>
                                                </td>
                                                <td class="step-label {{ in_array($status, ['shipped','on_the_way','delivered']) ? 'step-active' : '' }}">
                                                    Shipped
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="step-dot">
                                                    <span class="dot {{ in_array($status, ['on_the_way','delivered']) ? 'dot-active' : '' }}"></span>
                                                </td>
                                                <td class="step-label {{ in_array($status, ['on_the_way','delivered']) ? 'step-active' : '' }}">
                                                    On the way
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="step-dot">
                                                    <span class="dot {{ $status === 'delivered' ? 'dot-active' : '' }}"></span>
                                                </td>
                                                <td class="step-label {{ $status === 'delivered' ? 'step-active' : '' }}">
                                                    Delivered
                                                </td>
                                            </tr>
                                        </table>

                                        {{-- Tracking button (link to your website tracking page) --}}
                                        <p style="margin: 0 0 12px;">
                                            <a href="{{ url('/tracking/' . ($trackingCode ?? 'DE1234567890')) }}"
                                               class="btn"
                                               target="_blank">
                                                View tracking details
                                            </a>
                                        </p>

                                        {{-- Footer --}}
                                        <div class="footer">
                                            <p style="margin:0 0 6px;">
                                                You receive this message because you are a customer or subscriber of {{ config('app.name') }}.
                                            </p>
                                            <p style="margin:0 0 6px;">
                                                If you did not place this order, please contact us immediately by replying to this email.
                                            </p>
                                            <p style="margin:0 0 6px;">
                                                {{ config('app.name') }} · {{ config('app.company_address', 'Your Street 1, 12345 Your City, Germany') }}
                                            </p>
                                            <p style="margin:0;">
                                                To unsubscribe from future marketing emails, click
                                                <a href="{{ url('/unsubscribe') }}" target="_blank">here</a>.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>

                        {{-- Tracking pixel – invisible --}}
                        <tr>
                            <td style="height:1px; line-height:0;">
                                <img src="{{ $trackingUrl }}"
                                     alt=""
                                     width="1"
                                     height="1"
                                     style="display:block;border:0;outline:none;line-height:0;">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
