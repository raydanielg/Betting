<?php

namespace App\Constants;

class Status {

    const ENABLE  = 1;
    const DISABLE = 0;

    const YES = 1;
    const NO  = 0;

    const VERIFIED   = 1;
    const UNVERIFIED = 0;

    const PAYMENT_INITIATE = 0;
    const PAYMENT_SUCCESS  = 1;
    const PAYMENT_PENDING  = 2;
    const PAYMENT_REJECT   = 3;

    CONST TICKET_OPEN   = 0;
    CONST TICKET_ANSWER = 1;
    CONST TICKET_REPLY  = 2;
    CONST TICKET_CLOSE  = 3;

    CONST PRIORITY_LOW    = 1;
    CONST PRIORITY_MEDIUM = 2;
    CONST PRIORITY_HIGH   = 3;

    const USER_ACTIVE = 1;
    const USER_BAN    = 0;

    const KYC_UNVERIFIED = 0;
    const KYC_PENDING    = 2;
    const KYC_VERIFIED   = 1;

    const GOOGLE_PAY = 5001;

    const CUR_BOTH = 1;
    const CUR_TEXT = 2;
    const CUR_SYM  = 3;

    const SINGLE_BET = 1;
    const MULTI_BET  = 2;

    const BET_UNCONFIRMED = 0;
    const BET_WIN         = 1;
    const BET_PENDING     = 2;
    const BET_LOSS        = 3;
    const BET_REFUNDED    = 4;

    const FRACTION_ODDS = 1;
    const DECIMAL_ODDS  = 2;

    const MARKET_LOCKED   = 1;
    const MARKET_UNLOCKED = 0;

    const OUTCOME_LOCKED   = 1;
    const OUTCOME_UNLOCKED = 0;

    const GAME_NOT_OPEN_FOR_BETTING = 0;
    const GAME_OPEN_FOR_BETTING = 1;
    const GAME_CANCELLED = 2;
    const GAME_CLOSED_FOR_BETTING = 3;
    const GAME_ENDED = 4;

    const EVENT_TYPE_INDIVIDUAL = 0;
    const EVENT_TYPE_OUTRIGHT = 1;

    const ODDS_ONLY = 1;
    const SPREAD_POINT = 2;
    const OVER_UNDER = 3;
    const HANDICAP = 4;
}
