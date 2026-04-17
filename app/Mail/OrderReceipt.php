<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderReceipt extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * The order instance.
   *
   * @var \App\Models\Order
   */
  public Order $order;

  /**
   * Create a new message instance.
   */
  public function __construct(Order $order)
  {
    // make sure we have user + items for the email view
    $this->order = $order->loadMissing('items', 'user');
  }

  /**
   * Build the message.
   */
  public function build()
  {
    return $this
      ->subject('Your E&B Laundry Hub Order Receipt #' . $this->order->id)
      ->view('emails.order_receipt', [
        'order' => $this->order,
      ]);
  }
}
