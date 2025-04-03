<?php

namespace App\Notify;

use App\Notify\Notifiable;
use App\Notify\NotifyProcess;
use App\Notify\SmsGateway;

class Sms extends NotifyProcess implements Notifiable {

    /**
     * Mobile number of receiver
     *
     * @var string
     */
    public $mobile;

    /**
     * Assign value to properties
     *
     * @return void
     */
    public function __construct() {
        $this->statusField    = 'sms_status';
        $this->body           = 'sms_body';
        $this->globalTemplate = 'sms_template';
        $this->notifyConfig   = 'sms_config';
    }

    /**
     * Send notification
     *
     * @return void|bool
     */
    public function send() {
        if (!gs('sn')) {
            return false;
        }
        //get message from parent
        $message = $this->getMessage();
        if ($message) {
            try {
                $gateway = gs('sms_config')->name;
                if ($this->mobile) {
                    $sendSms          = new SmsGateway();
                    $sendSms->to      = $this->mobile;
                    $sendSms->from    = $this->getSmsFrom();
                    $sendSms->message = strip_tags($message);
                    $sendSms->config  = gs('sms_config');
                    $sendSms->$gateway();
                    $this->createLog('sms');
                }
            } catch (\Exception $e) {
                $this->createErrorLog('SMS Error: ' . $e->getMessage());
                session()->flash('sms_error', 'API Error: ' . $e->getMessage());
            }
        }

    }

    /**
     * Configure some properties
     *
     * @return void
     */
    public function prevConfiguration() {
        //Check If User
        if ($this->user) {
            $this->mobile       = $this->user->mobileNumber;
            $this->receiverName = $this->user->fullname;
        }
        $this->toAddress = $this->mobile;
    }

    private function getSmsFrom() {
        $this->sentFrom = $this->replaceTemplateShortCode($this->template->sms_sent_from ?? gs('sms_from'));
        return $this->sentFrom;
    }
}
