<?php

namespace App\Mail;

use App\Models\Order as OrderModel;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class OrderConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public OrderModel $order;
    public string $customerName;

    public function __construct(OrderModel $order)
    {
        $this->order = $order->load(['items', 'user']);

        $this->customerName = optional($this->order->user)->name
            ?: ($this->order->pickup_name ?: 'Customer');
    }

    public function build()
    {
        $pickupDate   = $this->fmtDate($this->order->pickup_date);
        $pickupTime   = $this->order->pickup_time ?: '—';
        $deliveryDate = $this->fmtDate($this->order->delivery_date);
        $deliveryTime = $this->order->delivery_time ?: '—';

        $subject = sprintf(
            'Order #%d confirmed • Pickup %s %s',
            $this->order->id,
            $pickupDate,
            $pickupTime
        );

        // Inline logo (CID)
        $logoCid = null;
        $this->withSymfonyMessage(function ($message) use (&$logoCid) {
            $logoCid = $message->embedFromPath(
                public_path('images/E&B_Logo.png'),
                'logo.png',
                'image/png'
            );
        });

        // Signed public receipt link (valid 7 days), honoring PUBLIC_URL if set
        $actionUrl = $this->signedPublicUrl(
            'orders.receipt.public',
            ['order' => $this->order->getKey()],
            now()->addDays(7)
        );

        // Prefer the wrapper layout if available; otherwise fall back to legacy view
        if (view()->exists('emails.layout.email_notify_customer') && view()->exists('emails.order_confirmed_body')) {
            return $this->subject($subject)
                ->view('emails.layout.email_notify_customer')
                ->with([
                    'order'        => $this->order,
                    'customerName' => $this->customerName,
                    'pickupDate'   => $pickupDate,
                    'pickupTime'   => $pickupTime,
                    'deliveryDate' => $deliveryDate,
                    'deliveryTime' => $deliveryTime,
                    'logoCid'      => $logoCid,
                    'actionUrl'    => $actionUrl,
                    // tell the wrapper which partial to include as the body
                    'bodyPartial'  => 'emails.order_confirmed_body',
                ]);
        }

        // Fallback: your original blade (it will safely ignore extra vars)
        return $this->subject($subject)
            ->view('emails.order-confirmed')
            ->with([
                'order'        => $this->order,
                'customerName' => $this->customerName,
                'pickupDate'   => $pickupDate,
                'pickupTime'   => $pickupTime,
                'deliveryDate' => $deliveryDate,
                'deliveryTime' => $deliveryTime,
                'logoCid'      => $logoCid,
                'actionUrl'    => $actionUrl,
            ]);
    }

    private function fmtDate($value): string
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : '—';
    }

    /**
     * Generate a temporary signed route using PUBLIC_URL as base (for mobile-friendly deep links).
     */
    private function signedPublicUrl(string $route, array $params, \DateTimeInterface $expires): string
    {
        $base = rtrim(env('PUBLIC_URL', config('app.url')), '/');

        /** @var \Illuminate\Routing\UrlGenerator $url */
        $url = app('url');

        $origBase   = config('app.url');
        $origScheme = parse_url($origBase, PHP_URL_SCHEME) ?: 'http';

        // Temporarily force generator to PUBLIC_URL
        $url->forceRootUrl($base);
        $url->forceScheme(parse_url($base, PHP_URL_SCHEME) ?: 'https');

        try {
            return URL::temporarySignedRoute($route, $expires, $params);
        } finally {
            // Restore original host/scheme
            $url->forceRootUrl($origBase);
            $url->forceScheme($origScheme);
        }
    }
}
