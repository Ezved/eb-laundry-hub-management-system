<?php
// added 11/17/2025
namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
  use Queueable, SerializesModels;

  public User $user;
  public string $verificationUrl;

  public function __construct(User $user, string $verificationUrl)
  {
    $this->user = $user;
    $this->verificationUrl = $verificationUrl;
  }

  public function build()
  {
    return $this->subject('Verify your E&B Laundry Hub account')
      ->view('emails.verify_email');
  }
}
