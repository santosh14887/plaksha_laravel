<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;

class WelcomeNotification extends Notification
{
    use Queueable;

    public function __construct(){
    }

    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }

    public function toExpoPush($notifiable)
    {
        return ExpoMessage::create()
            ->badge(1)
            ->title("Hello World!")
            ->enableSound()
            ->body("Hello World!")->setChannelId("default");
    }

    public function toArray($notifiable)
    {
        return [
        ];
    }
}