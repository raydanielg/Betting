<?php

namespace App\Lib;

use App\Models\CommissionLog;
use App\Models\ReferralSetting;
use App\Models\Transaction;

class Referral {
    public static function levelCommission($user, $amount, $trx, $commissionType = '') {
        $tempUser = $user;
        $i        = 1;
        $level    = ReferralSetting::where('commission_type', $commissionType)->count();

        while ($i <= $level) {
            $referer    = $tempUser->refBy;
            $commission = ReferralSetting::where('commission_type', $commissionType)->where('level', $i)->first();

            if (!$referer || !$commission) {
                break;
            }

            $commissionAmount = ($amount * $commission->percent) / 100;
            $referer->balance += $commissionAmount;
            $referer->save();

            $transactions[] = [
                'user_id'      => $referer->id,
                'amount'       => getAmount($commissionAmount, 8),
                'post_balance' => $referer->balance,
                'trx_type'     => '+',
                'details'      => 'Level ' . $i . ' referral commission From ' . $user->username,
                'remark'       => 'referral',
                'trx'          => $trx,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];

            $commissionLog[] = [
                'to_id'             => $referer->id,
                'from_id'           => $user->id,
                'level'             => $i,
                'post_balance'      => $referer->balance,
                'commission_amount' => $commissionAmount,
                'trx_amo'           => $amount,
                'title'             => 'Level ' . $i . ' referral commission from ' . $user->username,
                'type'              => $commissionType,
                'percent'           => $commission->percent,
                'trx'               => $trx,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];

            notify($referer, 'REFERRAL_COMMISSION', [
                'username'           => $referer->username,
                'amount'             => $commissionAmount,
                'trx'                => $trx,
                'commission_type'    => $commissionType,
                'level'              => $i,
                'commission_percent' => $commission->percent,
                'referral_user'      => $user->username,
            ]);

            $tempUser = $referer;
            $i++;
        }

        if (isset($transactions)) {
            Transaction::insert($transactions);
        }

        if (isset($commissionLog)) {
            CommissionLog::insert($commissionLog);
        }
    }
}
