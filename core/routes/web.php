<?php

use Illuminate\Support\Facades\Route;

Route::get('/clear', function () {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
});


Route::controller('CronController')->prefix('cron')->name('cron.')->group(function () {
    Route::get('fetch-leagues', 'CronController@fetchLeagues')->name('fetch.leagues');
    Route::get('fetch-games', 'CronController@fetchGames')->name('fetch.games');
    Route::get('fetch-odds', 'CronController@fetchOdds')->name('fetch.odds');
    Route::get('fetch-in-play-odds', 'CronController@fetchInPlayOdds')->name('fetch.running.game.odds');
    Route::get('set-open-for-betting', 'CronController@setOpenForBetting')->name('set.open.for.betting');
    Route::get('bet-win', 'CronController@win')->name('bet.win');
    Route::get('bet-lose', 'CronController@lose')->name('bet.loss');
    Route::get('bet-refund', 'CronController@refund')->name('bet.refund');
    Route::get('run-manually/{alias}', 'CronController@runManually')->name('manual.run');
});

// User Support Ticket
Route::controller('TicketController')->prefix('ticket')->name('ticket.')->group(function () {
    Route::get('/', 'supportTicket')->name('index');
    Route::get('new', 'openSupportTicket')->name('open');
    Route::post('create', 'storeSupportTicket')->name('store');
    Route::get('view/{ticket}', 'viewTicket')->name('view');
    Route::post('reply/{id}', 'replyTicket')->name('reply');
    Route::post('close/{id}', 'closeTicket')->name('close');
    Route::get('download/{attachment_id}', 'ticketDownload')->name('download');
});

Route::controller('BetSlipController')->prefix('bet')->name('bet.')->group(function () {
    Route::get('add-to-bet-slip', 'addToBetSlip')->name('slip.add');
    Route::post('remove/{id}', 'remove')->name('slip.remove');
    Route::post('remove-all', 'removeAll')->name('slip.remove.all');
    Route::post('update', 'update')->name('slip.update');
    Route::post('update-all', 'updateAll')->name('slip.update.all');
});

Route::controller('SiteController')->group(function () {
    Route::get('/contact', 'contact')->name('contact');
    Route::post('/contact', 'contactSubmit');
    Route::get('/change/{lang?}', 'changeLanguage')->name('lang');
    Route::get('/news', 'blog')->name('blog');
    Route::get('news/{slug}', 'blogDetails')->name('blog.details');
    Route::get('cookie-policy', 'cookiePolicy')->name('cookie.policy');
    Route::get('/cookie/accept', 'cookieAccept')->name('cookie.accept');
    Route::get('policy/{slug}', 'policyPages')->name('policy.pages');
    Route::get('placeholder-image/{size}', 'placeholderImage')->withoutMiddleware('maintenance')->name('placeholder.image');
    Route::get('maintenance-mode', 'maintenance')->withoutMiddleware('maintenance')->name('maintenance');

    Route::get('odds-by-market/{id}', 'getOdds')->name('market.odds');
    Route::get('markets/{gameSlug}', 'markets')->name('game.markets');
    Route::get('league/{slug}', 'gamesByLeague')->name('league.games');
    Route::get('category/{slug}', 'gamesByCategory')->name('category.games');
    Route::get('switch-to', 'switchType')->name('switch.type');
    Route::get('odds-type/{type}', 'oddsType')->name('odds.type');
    Route::get('/', 'index')->name('home');
});
