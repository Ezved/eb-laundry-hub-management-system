<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class DeliveryInProgressNotification extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public ?string $message = null) {}

    public function via($notifiable)
    {
        // Add 'database' if you also want to log it.
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $order = $this->order->loadMissing('user');

        // Signed, PUBLIC_URL-based link to a public receipt (no login)
        $actionUrl = $this->safeSignedPublicUrl(
            'orders.receipt.public',
            ['order' => $order->getKey()],
            now()->addDays(7),
            // Fallback if route/middleware not present
            function () use ($order) {
                try {
                    return route('user.orders.show', $order);
                } catch (\Throwable) {
                    return url('/'); // last-resort fallback
                }
            }
        );

        // If you have the wrapper layout, use it; else fall back to simple lines.
        if (view()->exists('emails.layout.email_notify_customer')) {
            return (new MailMessage)
                ->subject('Your Laundry Is Out for Delivery 📦')
                ->view('emails.layout.email_notify_customer', [
                    'bodyPartial'  => 'emails.notify_customer_of_delivery_in_progress',
                    'order'        => $order,
                    'customerName' => $notifiable->name ?? 'Customer',
                    'messageText'  => $this->message ?? null,
                    'actionUrl'    => $actionUrl,
                ]);
        }

        // Fallback: default Markdown mail
        return (new MailMessage)
            ->subject('Your Delivery Is In Progress')
            ->greeting('Hi ' . ($notifiable->name ?? 'there') . '!')
            ->line($this->message ?: 'Your delivery is now in progress.')
            ->line('Order #: ' . $order->id)
            ->action('View Your Order', $actionUrl)
            ->line('Thank you for choosing us!');
    }

    /**
     * Generate a signed route using PUBLIC_URL as the base (ngrok-friendly),
     * with a safe fallback if the route/middleware isn’t available.
     */
    private function safeSignedPublicUrl(string $route, array $params, \DateTimeInterface $expires, callable $fallback): string
    {
        $base = rtrim(env('PUBLIC_URL', config('app.url')), '/');
        /** @var \Illuminate\Routing\UrlGenerator $url */
        $url = app('url');

        $origBase   = config('app.url');
        $origScheme = parse_url($origBase, PHP_URL_SCHEME) ?: 'http';

        // Temporarily force generator to PUBLIC_URL host/scheme
        $url->forceRootUrl($base);
        $url->forceScheme(parse_url($base, PHP_URL_SCHEME) ?: 'https');

        try {
            return URL::temporarySignedRoute($route, $expires, $params);
        } catch (\Throwable) {
            return $fallback();
        } finally {
            // Restore original app URL so the rest of the app is unaffected
            $url->forceRootUrl($origBase);
            $url->forceScheme($origScheme);
        }
    }
}
