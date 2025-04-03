<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller {
    public function index() {
        $pageTitle   = 'Subscribers';
        $subscribers = Subscriber::orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.subscriber.index', compact('pageTitle', 'subscribers'));
    }

    public function sendEmailForm() {
        $pageTitle = 'Email to Subscribers';
        if (session()->has('SEND_NOTIFICATION_TO_SUBSCRIBER') && !request()->email_sent) {
            session()->forget('SEND_NOTIFICATION_TO_SUBSCRIBER');
        }
        return view('admin.subscriber.send_email', compact('pageTitle'));
    }

    public function remove($id) {
        $subscriber = Subscriber::findOrFail($id);
        $subscriber->delete();

        $notify[] = ['success', 'Subscriber deleted successfully'];
        return back()->withNotify($notify);
    }

    public function sendEmail(Request $request) {
        $request->validate([
            'message'      => 'required',
            'subject'      => 'required_if:via,email,push',
            'start'        => 'required|integer|gte:1',
            'batch'        => 'required|integer|gte:1',
            'cooling_time' => 'required|integer|gte:1',
        ]);

        $query = Subscriber::query();

        if (session()->has("SEND_NOTIFICATION_TO_SUBSCRIBER")) {
            $totalSubscriberCount = session('SEND_NOTIFICATION_TO_SUBSCRIBER')['total_subscriber'];
        } else {
            $totalSubscriberCount = (clone $query)->count() - ($request->start - 1);
        }

        if (!$totalSubscriberCount) {
            $notify[] = ['info', "No subscriber found."];
            return back()->withNotify($notify);
        }

        $subscribers = (clone $query)->skip($request->start - 1)->limit($request->batch)->get();

        foreach ($subscribers as $subscriber) {
            $receiverName = explode('@', $subscriber->email)[0];
            $user         = [
                'username' => $subscriber->email,
                'email'    => $subscriber->email,
                'fullname' => $receiverName,
            ];
            notify($user, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], ['email'], createLog: false);
        }

        return $this->sessionForNotification($totalSubscriberCount, $request);
    }

    private function sessionForNotification($totalSubscriberCount, $request) {
        if (session()->has('SEND_NOTIFICATION_TO_SUBSCRIBER')) {
            $sessionData = session("SEND_NOTIFICATION_TO_SUBSCRIBER");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData                     = $request->except('_token');
            $sessionData['total_sent']       = $request->batch;
            $sessionData['total_subscriber'] = $totalSubscriberCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalSubscriberCount) {
            session()->forget("SEND_NOTIFICATION_TO_SUBSCRIBER");
            $message = " Email notifications were sent successfully";
            $url     = route("admin.subscriber.send.email");
        } else {
            session()->put('SEND_NOTIFICATION_TO_SUBSCRIBER', $sessionData);
            $message = $sessionData['total_sent'] . " Email notifications were sent successfully";
            $url     = route("admin.subscriber.send.email") . "?email_sent=yes";
        }
        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }
}
