<?php
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
class ActivateNotification extends Notification{
    public function via($notifiable)
    {
        return [ExpoChannel::class];
    }
public function toExpoPush($notifiable){
        return ExpoMessage::create()
            ->badge(1)
            ->title("Hello World!")
            ->enableSound()
            ->body("Hello World!")
            ->setChannelId("chat-messages");
}
}