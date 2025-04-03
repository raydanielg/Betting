<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\NotificationTemplate;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class NotificationController extends Controller {
    public function globalEmail() {
        $pageTitle = 'Global Email Template';
        return view('admin.notification.global_email_template', compact('pageTitle'));
    }

    public function globalEmailUpdate(Request $request) {
        $request->validate([
            'email_from'      => 'required|email|string|max:40',
            'email_from_name' => 'required',
            'email_template'  => 'required',
        ]);

        $general                  = gs();
        $general->email_from      = $request->email_from;
        $general->email_from_name = $request->email_from_name;
        $general->email_template  = $request->email_template;
        $general->save();

        $notify[] = ['success', 'Global email template updated successfully'];
        return back()->withNotify($notify);
    }

    public function globalSms() {
        $pageTitle = 'Global Sms Template';
        return view('admin.notification.global_sms_template', compact('pageTitle'));
    }

    public function globalSmsUpdate(Request $request) {
        $request->validate([
            'sms_from'     => 'required|string|max:40',
            'sms_template' => 'required',
        ]);

        $general               = gs();
        $general->sms_from     = $request->sms_from;
        $general->sms_template = $request->sms_template;
        $general->save();

        $notify[] = ['success', 'Global sms template updated successfully'];
        return back()->withNotify($notify);
    }

    public function globalPush() {
        $pageTitle = 'Global Push Notification Template';
        return view('admin.notification.global_push_template', compact('pageTitle'));
    }

    public function globalPushUpdate(Request $request) {
        $request->validate([
            'push_template' => 'required',
            'push_title'    => 'required',
        ]);

        $general                = gs();
        $general->push_template = $request->push_template;
        $general->push_title    = $request->push_title;
        $general->save();

        $notify[] = ['success', 'Global push notification template updated successfully'];
        return back()->withNotify($notify);
    }

    public function templates() {
        $pageTitle = 'Notification Templates';
        $templates = NotificationTemplate::orderBy('name')->get();
        return view('admin.notification.template.index', compact('pageTitle', 'templates'));
    }

    public function templateEdit($type, $id) {
        $template  = NotificationTemplate::findOrFail($id);
        $pageTitle = $template->name;
        return view('admin.notification.template.' . $type, compact('pageTitle', 'template'));
    }

    public function templateUpdate(Request $request, $type, $id) {
        $validationRule = [];
        if ($type == 'email') {
            $validationRule = [
                'subject'    => 'required|string|max:255',
                'email_body' => 'required',
            ];
        }
        if ($type == 'sms') {
            $validationRule = [
                'sms_body' => 'required',
            ];
        }
        if ($type == 'push') {
            $validationRule = [
                'push_body' => 'required',
            ];
        }
        $request->validate($validationRule);
        $template = NotificationTemplate::findOrFail($id);
        if ($type == 'email') {
            $template->subject                 = $request->subject;
            $template->email_body              = $request->email_body;
            $template->email_sent_from_name    = $request->email_sent_from_name;
            $template->email_sent_from_address = $request->email_sent_from_address;
            $template->email_status            = $request->email_status ? Status::ENABLE : Status::DISABLE;
        }
        if ($type == 'sms') {
            $template->sms_body      = $request->sms_body;
            $template->sms_sent_from = $request->sms_sent_from;
            $template->sms_status    = $request->sms_status ? Status::ENABLE : Status::DISABLE;
        }
        if ($type == 'push') {
            $template->push_title  = $request->push_title;
            $template->push_body   = $request->push_body;
            $template->push_status = $request->push_status ? Status::ENABLE : Status::DISABLE;
        }
        $template->save();

        $notify[] = ['success', 'Notification template updated successfully'];
        return back()->withNotify($notify);
    }

    public function emailSetting() {
        $pageTitle = 'Email Notification Settings';
        return view('admin.notification.email_setting', compact('pageTitle'));
    }

    public function emailSettingUpdate(Request $request) {
        $request->validate([
            'email_method' => 'required|in:php,smtp,sendgrid,mailjet',
            'host'         => 'required_if:email_method,smtp',
            'port'         => 'required_if:email_method,smtp',
            'username'     => 'required_if:email_method,smtp',
            'password'     => 'required_if:email_method,smtp',
            'enc'          => 'required_if:email_method,smtp',
            'appkey'       => 'required_if:email_method,sendgrid',
            'public_key'   => 'required_if:email_method,mailjet',
            'secret_key'   => 'required_if:email_method,mailjet',
        ], [
            'host.required_if'       => 'The :attribute is required for SMTP configuration',
            'port.required_if'       => 'The :attribute is required for SMTP configuration',
            'username.required_if'   => 'The :attribute is required for SMTP configuration',
            'password.required_if'   => 'The :attribute is required for SMTP configuration',
            'enc.required_if'        => 'The :attribute is required for SMTP configuration',
            'appkey.required_if'     => 'The :attribute is required for SendGrid configuration',
            'public_key.required_if' => 'The :attribute is required for Mailjet configuration',
            'secret_key.required_if' => 'The :attribute is required for Mailjet configuration',
        ]);
        if ($request->email_method == 'php') {
            $data['name'] = 'php';
        } else if ($request->email_method == 'smtp') {
            $request->merge(['name' => 'smtp']);
            $data = $request->only('name', 'host', 'port', 'enc', 'username', 'password', 'driver');
        } else if ($request->email_method == 'sendgrid') {
            $request->merge(['name' => 'sendgrid']);
            $data = $request->only('name', 'appkey');
        } else if ($request->email_method == 'mailjet') {
            $request->merge(['name' => 'mailjet']);
            $data = $request->only('name', 'public_key', 'secret_key');
        }
        $general              = gs();
        $general->mail_config = $data;
        $general->save();
        $notify[] = ['success', 'Email settings updated successfully'];
        return back()->withNotify($notify);
    }

    public function emailTest(Request $request) {
        $request->validate([
            'email' => 'required|email',
        ]);

        $config       = gs('mail_config');
        $receiverName = explode('@', $request->email)[0];
        $subject      = strtoupper($config->name) . ' Configuration Success';
        $message      = 'Your email notification setting is configured successfully for ' . gs('site_name');

        if (gs('en')) {
            $user = [
                'username' => $request->email,
                'email'    => $request->email,
                'fullname' => $receiverName,
            ];
            notify($user, 'DEFAULT', [
                'subject' => $subject,
                'message' => $message,
            ], ['email'], false);
        } else {
            $notify[] = ['info', 'Please enable from general settings'];
            $notify[] = ['error', 'Your email notification is disabled'];
            return back()->withNotify($notify);
        }

        if (session('mail_error')) {
            $notify[] = ['error', session('mail_error')];
        } else {
            $notify[] = ['success', 'Email sent to ' . $request->email . ' successfully'];
        }

        return back()->withNotify($notify);
    }

    public function smsSetting() {
        $pageTitle = 'SMS Notification Settings';
        return view('admin.notification.sms_setting', compact('pageTitle'));
    }

    public function smsSettingUpdate(Request $request) {
        $request->validate([
            'sms_method'             => 'required|in:clickatell,infobip,messageBird,nexmo,smsBroadcast,twilio,textMagic,custom',
            'clickatell_api_key'     => 'required_if:sms_method,clickatell',
            'message_bird_api_key'   => 'required_if:sms_method,messageBird',
            'nexmo_api_key'          => 'required_if:sms_method,nexmo',
            'nexmo_api_secret'       => 'required_if:sms_method,nexmo',
            'infobip_username'       => 'required_if:sms_method,infobip',
            'infobip_password'       => 'required_if:sms_method,infobip',
            'sms_broadcast_username' => 'required_if:sms_method,smsBroadcast',
            'sms_broadcast_password' => 'required_if:sms_method,smsBroadcast',
            'text_magic_username'    => 'required_if:sms_method,textMagic',
            'apiv2_key'              => 'required_if:sms_method,textMagic',
            'account_sid'            => 'required_if:sms_method,twilio',
            'auth_token'             => 'required_if:sms_method,twilio',
            'from'                   => 'required_if:sms_method,twilio',
            'custom_api_method'      => 'required_if:sms_method,custom|in:get,post',
            'custom_api_url'         => 'required_if:sms_method,custom',
        ]);

        $data = [
            'name'          => $request->sms_method,
            'clickatell'    => [
                'api_key' => $request->clickatell_api_key,
            ],
            'infobip'       => [
                'username' => $request->infobip_username,
                'password' => $request->infobip_password,
            ],
            'message_bird'  => [
                'api_key' => $request->message_bird_api_key,
            ],
            'nexmo'         => [
                'api_key'    => $request->nexmo_api_key,
                'api_secret' => $request->nexmo_api_secret,
            ],
            'sms_broadcast' => [
                'username' => $request->sms_broadcast_username,
                'password' => $request->sms_broadcast_password,
            ],
            'twilio'        => [
                'account_sid' => $request->account_sid,
                'auth_token'  => $request->auth_token,
                'from'        => $request->from,
            ],
            'text_magic'    => [
                'username'  => $request->text_magic_username,
                'apiv2_key' => $request->apiv2_key,
            ],
            'custom'        => [
                'method'  => $request->custom_api_method,
                'url'     => $request->custom_api_url,
                'headers' => [
                    'name'  => $request->custom_header_name ?? [],
                    'value' => $request->custom_header_value ?? [],
                ],
                'body'    => [
                    'name'  => $request->custom_body_name ?? [],
                    'value' => $request->custom_body_value ?? [],
                ],
            ],
        ];
        $general             = gs();
        $general->sms_config = $data;
        $general->save();
        $notify[] = ['success', 'Sms settings updated successfully'];
        return back()->withNotify($notify);
    }

    public function smsTest(Request $request) {
        $request->validate(['mobile' => 'required']);
        if (gs('sn')) {
            $user = [
                'username'     => $request->mobile,
                'mobileNumber' => $request->mobile,
                'fullname'     => '',
            ];
            notify($user, 'DEFAULT', [
                'subject' => '',
                'message' => 'Your sms notification setting is configured successfully for ' . gs('site_name'),
            ], ['sms'], false);
        } else {
            $notify[] = ['info', 'Please enable from general settings'];
            $notify[] = ['error', 'Your sms notification is disabled'];
            return back()->withNotify($notify);
        }

        if (session('sms_error')) {
            $notify[] = ['error', session('sms_error')];
        } else {
            $notify[] = ['success', 'SMS sent to ' . $request->mobile . 'successfully'];
        }

        return back()->withNotify($notify);
    }

    public function pushSetting() {
        $pageTitle  = 'Push Notification Settings';
        $fileExists = file_exists(getFilePath('pushConfig') . '/push_config.json');
        return view('admin.notification.push_setting', compact('pageTitle', 'fileExists'));
    }

    public function pushSettingUpdate(Request $request) {

        $request->validate([
            'apiKey'            => 'required',
            'authDomain'        => 'required',
            'projectId'         => 'required',
            'storageBucket'     => 'required',
            'messagingSenderId' => 'required',
            'appId'             => 'required',
            'measurementId'     => 'required',
        ]);

        $data = [
            'apiKey'            => $request->apiKey,
            'authDomain'        => $request->authDomain,
            'projectId'         => $request->projectId,
            'storageBucket'     => $request->storageBucket,
            'messagingSenderId' => $request->messagingSenderId,
            'appId'             => $request->appId,
            'measurementId'     => $request->measurementId,
        ];

        $general                  = gs();
        $general->firebase_config = $data;
        $general->save();

        try {
            $jsPath = 'assets/global/js/firebase/configs.js';
            $config = "var firebaseConfig = " . json_encode(gs('firebase_config'));
            file_put_contents($jsPath, $config);
        } catch (\Exception $e) {
            $notify[] = ['error', $e->getMessage()];
            return back()->withNotify($notify);
        }

        $notify[] = ['success', 'Firebase settings updated successfully'];
        return back()->withNotify($notify);
    }

    public function pushSettingUpload(Request $request) {
        $request->validate([
            'file' => ['required', new FileTypeValidate(['json'])],
        ]);
        try {
            fileUploader($request->file, getFilePath('pushConfig'), filename: 'push_config.json');
        } catch (\Exception $exp) {
            $notify[] = ['error', 'Couldn\'t upload your file'];
            return back()->withNotify($notify);
        }
        $notify[] = ['success', 'Configuration file uploaded successfully'];
        return back()->withNotify($notify);
    }
    public function pushSettingDownload() {
        $filePath = getFilePath('pushConfig') . '/push_config.json';
        if (!file_exists($filePath)) {
            $notify[] = ['success', "File not found"];
            return back()->withNotify($notify);
        }
        return response()->download($filePath);
    }

}
