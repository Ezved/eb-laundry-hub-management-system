<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL; // added: 11/11/2025

class PickupOnTheWayNotification extends Notification
{
  use Queueable;

  public function __construct(public Order $order, public ?string $message = null) {}

  public function via($notifiable)
  {
    return ['mail'];
  }

  public function toMail($notifiable) // added: 11/11/2025
  {
    $order = $this->order->loadMissing('user');

    $actionUrl = $this->signedPublicUrl( // added: 11/11/2025
      'orders.receipt.public',
      ['order' => $order->getKey()],
      now()->addDays(7)
    );

    return (new \Illuminate\Notifications\Messages\MailMessage)
      ->subject('Your Rider Is On the Way 🚚')
      ->view('emails.layout.email_notify_customer', [
        'bodyPartial'  => 'emails.notify_customer_pick_up_on_the_way',
        'order'        => $order,
        'customerName' => $notifiable->name ?? 'Customer',
        'messageText'  => $this->message,
        'actionUrl'    => $actionUrl,
      ]);
  }

  private function signedPublicUrl(string $route, array $params, \DateTimeInterface $expires): string // added: 11/11/2025
  {
    $base = rtrim(env('PUBLIC_URL', config('app.url')), '/');
    /** @var \Illuminate\Routing\UrlGenerator $url */
    $url = app('url');

    $origBase   = config('app.url');
    $origScheme = parse_url($origBase, PHP_URL_SCHEME) ?: 'http';

    $url->forceRootUrl($base);
    $url->forceScheme(parse_url($base, PHP_URL_SCHEME) ?: 'https');

    try {
      return \Illuminate\Support\Facades\URL::temporarySignedRoute($route, $expires, $params);
    } finally {
      $url->forceRootUrl($origBase);
      $url->forceScheme($origScheme);
    }
  }
}
