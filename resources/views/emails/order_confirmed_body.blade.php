<div class="content">
    <h2>Hello {{ $customerName }},</h2>
    <p>We're happy to let you know that your order has been successfully <strong>confirmed!</strong></p>
    <p>Below are your pickup and delivery details:</p>

    <div class="details">
        <p><strong>Pickup Date:</strong> {{ $pickupDate }}</p>
        <p><strong>Pickup Time:</strong> {{ $pickupTime }}</p>
        <p><strong>Delivery Date:</strong> {{ $deliveryDate }}</p>
        <p><strong>Delivery Time:</strong> {{ $deliveryTime }}</p>
        <p><strong>Total Amount:</strong> ₱-----</p>
        <p><strong>Payment Method:</strong> {{ strtoupper($order->payment_method) }}</p>
    </div>

    <p>We'll notify you once your laundry is ready for delivery.
        Thank you for choosing <span class="highlight">E&B Laundry Hub</span> — we appreciate your trust!</p>

    @isset($actionUrl)
        <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin-top:20px;">
            <tr>
                <td align="center" bgcolor="#173F7B" style="border-radius:6px;">
                    <a href="{{ $actionUrl }}" target="_blank"
                        style="display:inline-block;padding:12px 22px;font-family:Arial,sans-serif;font-size:16px;
                  line-height:20px;text-decoration:none;color:#ffffff;">View
                        Receipt</a>
                </td>
            </tr>
        </table>
    @endisset
</div>
