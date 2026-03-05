@php
    $customer = $domain->customer;
    $daysText = match ($type) {
        '30days' => '30 days',
        '7days' => '7 days',
        'expired' => 'today',
        default => '',
    };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Domain expiry reminder</title>
</head>
<body style="font-family: Arial, sans-serif; background:#f3f4f6; padding:24px;">
    <table width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;padding:24px;">
        <tr>
            <td>
                <h2 style="margin:0 0 12px 0;color:#111827;">Domain renewal reminder</h2>
                <p style="margin:0 0 16px 0;color:#4b5563;font-size:14px;">
                    Dear {{ $customer?->name ?? 'Customer' }},
                </p>
                <p style="margin:0 0 16px 0;color:#4b5563;font-size:14px;">
                    This is a friendly reminder that your domain
                    <strong>{{ $domain->domain_name }}</strong>
                    will expire {{ $daysText }} on
                    <strong>{{ optional($domain->expiry_date)->format('d M Y') }}</strong>.
                </p>
                <p style="margin:0 0 16px 0;color:#4b5563;font-size:14px;">
                    Hosting plan: {{ $domain->plan_name ?? 'N/A' }}<br>
                    Server: {{ $domain->server?->name ?? 'N/A' }}<br>
                    Price: {{ $domain->price ? number_format($domain->price, 2) : 'N/A' }}
                </p>
                <p style="margin:0 0 16px 0;color:#4b5563;font-size:14px;">
                    Please contact us to renew this domain before it expires to avoid service interruption.
                </p>
                <p style="margin:0;color:#9ca3af;font-size:12px;">
                    This email was generated automatically by your Host Renewal system.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

