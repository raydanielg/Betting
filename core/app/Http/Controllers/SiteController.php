<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Deposit;
use App\Models\Frontend;
use App\Models\Game;
use App\Models\Language;
use App\Models\League;
use App\Models\Outcome;
use App\Models\Page;
use App\Models\SupportMessage;
use App\Models\SupportTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class SiteController extends Controller {

    private function getActiveLeagueAndCategory($leagueSlug, $categorySlug) {
        $activeCategory = Category::active();
        if ($categorySlug) {
            $activeCategory->where('slug', $categorySlug);
        } else {
            $activeCategory->orderBy('name');
        }

        $activeCategory = $activeCategory->first();

        if ($leagueSlug) {
            $activeLeague   = League::activeForUser()->where('slug', $leagueSlug)->firstOrFail();
            $activeCategory = $activeLeague?->category;
        } else {
            $activeLeague = League::activeForUser()->hasOpenForBettingGames()->where('category_id', $activeCategory?->id)->whereHas('games')->orderBy('name', 'Asc')->first();
        }

        return [$activeLeague, $activeCategory];
    }

    private function homePageMarkets() {
        return ['h2h', 'h2h_3way', 'spreads', 'totals', 'outrights'];
    }

    public function index($categorySlug = null, $leagueSlug = null) {
        $pageTitle      = 'Home';

        $reference = @$_GET['reference'];
        if ($reference) {
            session()->put('reference', $reference);
        }

        $gameType       = session('game_type');
        $activeData     = $this->getActiveLeagueAndCategory($leagueSlug, $categorySlug);
        $activeLeague   = $activeData[0];
        $activeCategory = $activeData[1];
        $leagues        = League::activeForUser()->hasOpenForBettingGames()->where('category_id', $activeCategory?->id)->orderBy('name')->get();

        $games = Game::openForBetting()
            ->where('league_id', $activeLeague?->id)
            ->when(session('game_type') == 'live', function ($query) {
                $query->inPlay();
            })
            ->with([
                'teamOne:id,name,short_name,image',
                'teamTwo:id,name,short_name,image',
                'markets' => function ($q) use ($activeLeague) {
                    $q->active()
                        ->resultUndeclared()
                        ->whereIn('market_type', $this->homePageMarkets())
                        ->when($activeLeague?->has_outrights, function ($query) {
                            $query->orWhere('market_type', 'outrights');
                        })
                        ->with('outcomes', function ($outcome) {
                            $outcome->active();
                        });
                },
            ])
            ->orderBy('id', 'desc')
            ->get();

        return view('Template::home', compact('pageTitle', 'leagues', 'games', 'activeCategory', 'activeLeague'));
    }

    public function gamesByLeague($slug) {
        return $this->index(leagueSlug: $slug);
    }
    public function gamesByCategory($slug) {
        return $this->index(categorySlug: $slug);
    }

    public function switchType() {
        $url = url()->previous() ?? '/';
        if (session()->has('game_type')) {
            session()->forget('game_type');
        } else {
            session()->put('game_type', 'live');
        }

        return redirect($url);
    }

    public function oddsType($type) {
        session()->put('odds_type', $type);
        return redirect()->back();
    }

    public function markets($gameSlug) {
        $gameType = session('game_type');
        $game     = Game::openForBetting();

        if (session('game_type') == 'live') {
            $game->inPlay();
        }
        $game = $game->where('slug', $gameSlug)
            ->hasActiveCategory()
            ->hasActiveLeague()
            ->with([
                'league',
                'markets'          => function ($market) {
                    $market->active()->filterByGamePeriod();
                    if (request()->more) {
                        $market->limit(request()->more);
                    }
                    $market->orderBy('id', 'asc')->resultUndeclared();
                },
                'markets.outcomes' => function ($outcome) {
                    $outcome->orderBy('id', 'desc')->active();
                },
            ])->firstOrFail();

        $pageTitle = "$game->slug - odds";
        return view('Template::markets', compact('pageTitle', 'game'));
    }

    public function getOdds($id) {
        $outcomes = Outcome::query();
        if (session('game_type') == 'live') {
            $outcomes->availableForBet();
        }
        $outcomes = $outcomes->where('market_id', $id)->with('market')->get();
        return view('Template::partials.odds_by_market', compact('outcomes'));
    }

    public function pages($slug) {
        $page        = Page::where('tempname', activeTemplate())->where('slug', $slug)->firstOrFail();
        $pageTitle   = $page->name;
        $sections    = $page->secs;
        $seoContents = $page->seo_content;
        $seoImage    = @$seoContents->image ? getImage(getFilePath('seo') . '/' . @$seoContents->image, getFileSize('seo')) : null;
        return view('Template::pages', compact('pageTitle', 'sections', 'seoContents', 'seoImage'));
    }

    public function contact() {
        $pageTitle = "Contact Us";
        $user      = auth()->user();
        return view('Template::contact', compact('pageTitle', 'user'));
    }

    public function contactSubmit(Request $request) {

        $request->validate([
            'name'    => 'required',
            'email'   => 'required',
            'subject' => 'required|string|max:255',
            'message' => 'required',
        ]);

        $request->session()->regenerateToken();

        if (!verifyCaptcha()) {
            $notify[] = ['error', 'Invalid captcha provided'];
            return back()->withNotify($notify);
        }

        $random = getNumber();

        $ticket           = new SupportTicket();
        $ticket->user_id  = auth()->id() ?? 0;
        $ticket->name     = $request->name;
        $ticket->email    = $request->email;
        $ticket->priority = Status::PRIORITY_MEDIUM;

        $ticket->ticket     = $random;
        $ticket->subject    = $request->subject;
        $ticket->last_reply = Carbon::now();
        $ticket->status     = Status::TICKET_OPEN;
        $ticket->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->user() ? auth()->user()->id : 0;
        $adminNotification->title     = 'A new contact message has been submitted';
        $adminNotification->click_url = route('admin.ticket.view', $ticket->id);
        $adminNotification->save();

        $message                    = new SupportMessage();
        $message->support_ticket_id = $ticket->id;
        $message->message           = $request->message;
        $message->save();

        $notify[] = ['success', 'Ticket created successfully!'];

        return to_route('ticket.view', [$ticket->ticket])->withNotify($notify);
    }

    public function policyPages($slug) {
        $policy      = Frontend::where('slug', $slug)->where('data_keys', 'policy_pages.element')->firstOrFail();
        $pageTitle   = $policy->data_values->title;
        $seoContents = $policy->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('policy_pages', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::policy', compact('policy', 'pageTitle', 'seoContents', 'seoImage'));
    }

    public function changeLanguage($lang = null) {
        $language = Language::where('code', $lang)->first();
        if (!$language) {
            $lang = 'en';
        }

        session()->put('lang', $lang);
        return back();
    }

    public function blog() {
        $pageTitle = "News and Updates";
        $blogs     = Frontend::where('data_keys', 'blog.element')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('Template::blog', compact('pageTitle', 'blogs'));
    }

    public function blogDetails($slug) {
        $blog        = Frontend::where('slug', $slug)->where('data_keys', 'blog.element')->firstOrFail();
        $pageTitle   = 'Read Full News';
        $latestBlogs = Frontend::where('id', '!=', $blog->id)->where('data_keys', 'blog.element')->orderBy('id', 'desc')->limit(10)->get();
        $seoContents = $blog->seo_content;
        $seoImage    = @$seoContents->image ? frontendImage('blog', $seoContents->image, getFileSize('seo'), true) : null;
        return view('Template::blog_details', compact('blog', 'pageTitle', 'seoContents', 'seoImage', 'latestBlogs'));
    }

    public function cookieAccept() {
        Cookie::queue('gdpr_cookie', gs('site_name'), 43200);
    }

    public function cookiePolicy() {
        $cookieContent = Frontend::where('data_keys', 'cookie.data')->first();
        abort_if($cookieContent->data_values->status != Status::ENABLE, 404);
        $pageTitle = 'Cookie Policy';
        $cookie    = Frontend::where('data_keys', 'cookie.data')->first();
        return view('Template::cookie', compact('pageTitle', 'cookie'));
    }

    public function placeholderImage($size = null) {
        $pattern = '/\d+x\d+/';
        if (preg_match($pattern, $size)) {

            $imgWidth  = explode('x', $size)[0];
            $imgHeight = explode('x', $size)[1];
            $text      = $imgWidth . '×' . $imgHeight;
            $color     = [100, 100, 100];
            $bgColor   = [255, 255, 255];
            $fontSize  = round(($imgWidth - 50) / 8);
        } else {
            $text      = $size;
            $imgWidth  = 50;
            $imgHeight = 50;

            $color    = [255, 255, 255];
            $bgColor  = generateRandomColor();
            $fontSize = 22;
        }

        $fontFile = realpath('assets/font/solaimanLipi_bold.ttf');

        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if ($imgHeight < 100 && $fontSize > 30) {
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, ...$color);
        $bgFill    = imagecolorallocate($image, ...$bgColor);
        imagefill($image, 0, 0, $bgFill);
        $textBox    = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }

    public function maintenance() {
        $pageTitle = 'Maintenance Mode';
        if (gs('maintenance_mode') == Status::DISABLE) {
            return to_route('home');
        }
        $maintenance = Frontend::where('data_keys', 'maintenance.data')->first();
        return view('Template::maintenance', compact('pageTitle', 'maintenance'));
    }
}
