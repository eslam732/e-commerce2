<?php

namespace App\Notifications;

use App\Models\Product;
use App\Models\ProductComment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Comment extends Notification
{
    use Queueable;

    public $user;

    public $product;
    public $comment;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, Product $product, ProductComment $comment)
    {
        $this->user = $user;
        $this->product = $product;
        $this->comment = $comment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
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
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            "type" => 'Comment',
            "comment_id" => $this->comment->id,
            "info" => $this->user->name . " commented on your product " . $this->product->name,
            "user_id" => $this->user->id,
            "user_name" => $this->user->name,
            "product_id" => $this->product->id,

        ];
    }
}
