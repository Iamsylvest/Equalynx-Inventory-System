<?php

namespace App\Services;

use Twilio\Rest\Client;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $this->from = config('services.twilio.from');
        $this->client = new Client($sid, $token);
    }

    public function sendSMS($to, $message)
    {
        return $this->client->messages->create($to, [
            'from' => $this->from,
            'body' => $message
        ]);
    }
}