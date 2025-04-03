<?php

namespace App\Lib;

use App\Constants\Status;
use App\Models\NotificationTemplate;
use App\Models\User;

/**
 * Class UserNotificationSender
 *
 * This class handles the sending of notifications to users based on specified criteria.
 * It supports multiple notification channels (e.g., email, push notifications) and manages
 * session data to track the notification sending process.
 */
class UserNotificationSender {

    private $isSingleNotification = false;

    /**
     * Send notifications to all or selected users based on the request parameters.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notificationToAll($request) {
        if (!$this->isTemplateEnabled($request->via)) {
            return $this->redirectWithNotify('warning', 'Default notification template is not enabled');
        }

        $handleSelectedUser = $this->handleSelectedUsers($request);
        if (!$handleSelectedUser) {
            return $this->redirectWithNotify('error', "Ensure that the user field is populated when sending an email to the designated user group");
        }

        $userQuery      = $this->getUserQuery($request);
        $totalUserCount = $this->getTotalUserCount($userQuery, $request);

        if ($totalUserCount <= 0) {
            return $this->redirectWithNotify('error', "Notification recipients were not found among the selected user base.");
        }

        $imageUrl = $this->handlePushNotificationImage($request);
        $users    = $this->getUsers($userQuery, $request->start, $request->batch);

        $this->sendNotifications($users, $request, $imageUrl);

        return $this->manageSessionForNotification($totalUserCount, $request);
    }

    /**
     * Send a notification to a single user.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $userId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function notificationToSingle($request, $userId) {
        if (!$this->isTemplateEnabled($request->via)) {
            return $this->redirectWithNotify('warning', 'Default notification template is not enabled');
        }
        $this->isSingleNotification = true;
        $imageUrl = $this->handlePushNotificationImage($request);
        $user     = User::findOrFail($userId);

        $this->sendNotifications($user, $request, $imageUrl, true);

        return $this->redirectWithNotify("success", "Notification sent successfully");
    }
    /**
     * Check if the notification template is enabled for the specified channel.
     *
     * @param string $via
     * @return bool
     */
    private function isTemplateEnabled($via) {
        return NotificationTemplate::where('act', 'DEFAULT')->where($via . '_status', Status::ENABLE)->exists();
    }

    /**
     * Redirect with a notification message.
     *
     * @param string $type
     * @param string $message
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectWithNotify($type, $message) {
        $notify[] = [$type, $message];
        return back()->withNotify($notify);
    }

    /**
     * Handle selected users logic, merging user data from session if necessary.
     *
     * @param \Illuminate\Http\Request $request
     * @return bol
     */
    private function handleSelectedUsers($request) {
        if ($request->being_sent_to == 'selectedUsers') {
            if (session()->has("SEND_NOTIFICATION")) {
                $request->merge(['user' => session()->get('SEND_NOTIFICATION')['user']]);
            } elseif (!$request->user || !is_array($request->user) || empty($request->user)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get user query based on the scope.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function getUserQuery($request) {
        $scope = $request->being_sent_to;
        return User::oldest()->active()->$scope();
    }

    /**
     * Get the total user count for notification.
     *
     * @param \Illuminate\Database\Eloquent\Builder $userQuery
     * @param \Illuminate\Http\Request $request
     * @return int
     */
    private function getTotalUserCount($userQuery, $request) {
        if (session()->has("SEND_NOTIFICATION")) {
            $totalUserCount = session('SEND_NOTIFICATION')['total_user'];
        } else {
            $totalUserCount = (clone $userQuery)->count() - ($request->start - 1);
        }
        return $totalUserCount;
    }

    /**
     * Handle image upload for push notifications.
     *
     * @param \Illuminate\Http\Request $request
     * @return string|null
     */
    private function handlePushNotificationImage($request) {
        if ($request->via == 'push') {
            if ($request->hasFile('image')) {
                $imageUrl = fileUploader($request->image, getFilePath('push'));
                session()->put('PUSH_IMAGE_URL', $imageUrl);
                return $imageUrl;
            }
            return $this->isSingleNotification ? null : session()->get('PUSH_IMAGE_URL');
        }
        return null;
    }

    /**
     * Get users for notification based on pagination.
     *
     * @param \Illuminate\Database\Eloquent\Builder $userQuery
     * @param int $start
     * @param int $batch
     * @return \Illuminate\Support\Collection
     */
    private function getUsers($userQuery, $start, $batch) {
        return (clone $userQuery)->skip($start - 1)->limit($batch)->get();
    }

    /**
     * Send notifications to users.
     *
     * @param \Illuminate\Support\Collection $users
     * @param \Illuminate\Http\Request $request
     * @param string|null $imageUrl
     * @param bol $isSingleNotification
     * @return void
     */
    private function sendNotifications($users, $request, $imageUrl, $isSingleNotification = false) {
        if (!$isSingleNotification) {
            foreach ($users as $user) {
                notify($user, 'DEFAULT', [
                    'subject' => $request->subject,
                    'message' => $request->message,
                ], [$request->via], pushImage: $imageUrl);
            }
        } else {
            notify($users, 'DEFAULT', [
                'subject' => $request->subject,
                'message' => $request->message,
            ], [$request->via], pushImage: $imageUrl);
        }
    }

    /**
     * Manage session data for notification sending process.
     *
     * @param int $totalUserCount
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    private function manageSessionForNotification($totalUserCount, $request) {
        if (session()->has('SEND_NOTIFICATION')) {
            $sessionData                = session("SEND_NOTIFICATION");
            $sessionData['total_sent'] += $sessionData['batch'];
        } else {
            $sessionData               = $request->except('_token', 'image');
            $sessionData['total_sent'] = $request->batch;
            $sessionData['total_user'] = $totalUserCount;
        }

        $sessionData['start'] = $sessionData['total_sent'] + 1;

        if ($sessionData['total_sent'] >= $totalUserCount) {
            session()->forget("SEND_NOTIFICATION");
            $message = ucfirst($request->via) . " notifications were sent successfully";
            $url     = route("admin.users.notification.all");
        } else {
            session()->put('SEND_NOTIFICATION', $sessionData);
            $message = $sessionData['total_sent'] . " " . $sessionData['via'] . "  notifications were sent successfully";
            $url     = route("admin.users.notification.all") . "?email_sent=yes";
        }

        $notify[] = ['success', $message];
        return redirect($url)->withNotify($notify);
    }
}
