<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class MailNotification extends Notification
{
    use Queueable;

    private $details;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject("Success Registration Smart Schools")
                    ->greeting($this->details['greeting'])
                    ->line($this->details['body'])
                    ->with($this->details['body1'])
                    ->with($this->details['body2'])
                    ->with($this->details['body3'])
                    ->with($this->details['body4'])
                    ->with($this->details['body5'])
                    ->salutation("Enjoy")
                    ->action($this->details['actionText'], $this->details['actionURL'])
                    ->line($this->details['thanks']);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'order_id' => $this->details['order_id']
        ];
    }
}
