@if(!empty($user->name))
Hi {{ $user->name }},
@else
Hi,
@endif

Your iPhone 17 Pro Max is on the way.

Order details:
- Product: iPhone 17 Pro Max – 256 GB, Titanium
- Price: {{ $price ?? '1.599,00 €' }}
- Order number: {{ $orderNumber ?? 'AB-123456' }}
- Tracking code: {{ $trackingCode ?? 'DE1234567890' }}
- Status: On the way

Track your order here:
{{ url('/tracking/' . ($trackingCode ?? 'DE1234567890')) }}

If you did not place this order, please reply to this email or contact our support.

You receive this message because you are a customer or subscriber of {{ config('app.name') }}.
To unsubscribe from future marketing emails, visit:
{{ url('/unsubscribe') }}
