<?php
/**
 * Twilio integration class for Call-Survey
 */

namespace CallSurvey;

use Twilio\Rest\Client;

class TwilioService {
    private $client;
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
        $this->client = new Client(
            $config['account_sid'],
            $config['auth_token']
        );
    }
    
    /**
     * Make an outbound call for survey
     */
    public function makeCall($toNumber, $surveyUrl) {
        try {
            $call = $this->client->calls->create(
                $toNumber,
                $this->config['phone_number'],
                [
                    'url' => $surveyUrl,
                    'method' => 'POST',
                    'statusCallback' => $surveyUrl . '?callback=true',
                ]
            );
            return ['success' => true, 'sid' => $call->sid];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send SMS survey invitation
     */
    public function sendSMS($toNumber, $message) {
        try {
            $sms = $this->client->messages->create(
                $toNumber,
                [
                    'from' => $this->config['phone_number'],
                    'body' => $message,
                ]
            );
            return ['success' => true, 'sid' => $sms->sid];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Send WhatsApp survey invitation
     */
    public function sendWhatsApp($toNumber, $message) {
        try {
            $whatsapp = $this->client->messages->create(
                'whatsapp:' . $toNumber,
                [
                    'from' => $this->config['whatsapp_number'],
                    'body' => $message,
                ]
            );
            return ['success' => true, 'sid' => $whatsapp->sid];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
