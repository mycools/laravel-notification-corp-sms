<?php

namespace NotificationChannels\CorpSMS;

use Illuminate\Notifications\Notification;
use NotificationChannels\CorpSMS\Exceptions\CouldNotSendNotification;

class CorpSMSChannel
{
    /** @var \NotificationChannels\CorpSMS\CorpSMSApi */
    protected $smsc;

    public function __construct(CorpSMSApi $smsc)
    {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! ($to = $this->getRecipients($notifiable, $notification))) {
            return;
        }

        $message = $notification->{'toCorpSMS'}($notifiable);

        if (\is_string($message)) {
            $message = new CorpSMSMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    /**
     * Gets a list of phones from the given notifiable.
     *
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     *
     * @return string[]
     */
    protected function getRecipients($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('CorpSMS', $notification);

        if ($to === null || $to === false || $to === '') {
            return [];
        }

        return is_array($to) ? $to : [$to];
    }

    protected function sendMessage($recipients, CorpSMSMessage $message)
    {
        if (\mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'phones'  => \implode(',', $recipients),
            'msg'     => $message->content,
            'sender'  => $message->from,
            'tid' 	  => md5(time()), 
        ];

        if ($message->sendAt instanceof \DateTimeInterface) {
            $params['time'] = '0'.$message->sendAt->getTimestamp();
        }

        $this->smsc->send($params);
    }
}
