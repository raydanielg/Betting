<?php
namespace App\Traits;

use App\Constants\Status;

trait UserNotify {
    public static function notifyToUser() {
        return [
            'allUsers'               => 'All Bettors',
            'selectedUsers'          => 'Selected Bettors',
            'kycUnverified'          => 'Kyc Unverified Bettors',
            'kycVerified'            => 'Kyc Verified Bettors',
            'kycPending'             => 'Kyc Pending Bettors',
            'withBalance'            => 'Bettors having Balance',
            'emptyBalanceUsers'      => 'Bettors Having No Balance',
            'twoFaDisableUsers'      => '2FA: Disable Bettor',
            'twoFaEnableUsers'       => '2FA: Enable Bettor',
            'hasDepositedUsers'      => 'Bettors Having at Least One Deposit',
            'notDepositedUsers'      => 'Bettors Having No Deposit',
            'pendingDepositedUsers'  => 'Bettors Having Pending Deposit',
            'rejectedDepositedUsers' => 'Bettors Having Rejected Deposit',
            'topDepositedUsers'      => 'Top Deposited Bettors',
            'hasWithdrawUsers'       => 'Bettors Having at Least One Withdrawals',
            'pendingWithdrawUsers'   => 'Bettors Having Pending Withdrawals',
            'rejectedWithdrawUsers'  => 'Bettors Having Rejected Withdrawals',
            'pendingTicketUser'      => 'Bettors Having Pending Tickets',
            'answerTicketUser'       => 'Bettors Having Answer Ticket',
            'closedTicketUser'       => 'Bettors Having Closed Ticket',
            'notLoginUsers'          => 'Bettors Who Ddidn\'t Login in Last Few Days',
        ];
    }

    public function scopeSelectedUsers($query) {
        return $query->whereIn('id', request()->user ?? []);
    }

    public function scopeAllUsers($query) {
        return $query;
    }

    public function scopeEmptyBalanceUsers($query) {
        return $query->where('balance', '<=', 0);
    }

    public function scopeTwoFaDisableUsers($query) {
        return $query->where('ts', Status::DISABLE);
    }

    public function scopeTwoFaEnableUsers($query) {
        return $query->where('ts', Status::ENABLE);
    }

    public function scopeHasDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        });
    }

    public function scopeNotDepositedUsers($query) {
        return $query->whereDoesntHave('deposits', function ($q) {
            $q->successful();
        });
    }

    public function scopePendingDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->pending();
        });
    }

    public function scopeRejectedDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->rejected();
        });
    }

    public function scopeTopDepositedUsers($query) {
        return $query->whereHas('deposits', function ($deposit) {
            $deposit->successful();
        })->withSum(['deposits' => function ($q) {
            $q->successful();
        }], 'amount')->orderBy('deposits_sum_amount', 'desc')->take(request()->number_of_top_deposited_user ?? 10);
    }

    public function scopeHasWithdrawUsers($query) {
        return $query->whereHas('withdrawals', function ($q) {
            $q->approved();
        });
    }

    public function scopePendingWithdrawUsers($query) {
        return $query->whereHas('withdrawals', function ($q) {
            $q->pending();
        });
    }

    public function scopeRejectedWithdrawUsers($query) {
        return $query->whereHas('withdrawals', function ($q) {
            $q->rejected();
        });
    }

    public function scopePendingTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {
            $q->whereIn('status', [Status::TICKET_OPEN, Status::TICKET_REPLY]);
        });
    }

    public function scopeClosedTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {
            $q->where('status', Status::TICKET_CLOSE);
        });
    }

    public function scopeAnswerTicketUser($query) {
        return $query->whereHas('tickets', function ($q) {

            $q->where('status', Status::TICKET_ANSWER);
        });
    }

    public function scopeNotLoginUsers($query) {
        return $query->whereDoesntHave('loginLogs', function ($q) {
            $q->whereDate('created_at', '>=', now()->subDays(request()->number_of_days ?? 10));
        });
    }

    public function scopeKycVerified($query) {
        return $query->where('kv', Status::KYC_VERIFIED);
    }

}
