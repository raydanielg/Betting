<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\League;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class LeagueController extends Controller {
    public $pageTitle;

    private function leagues($scope = null)  {
        $leagues    = League::query();

        if($scope) {
            $leagues->$scope('running');
        }

        $leagues->searchable(['name', 'slug', 'short_name'])->filter(['category_id', 'has_outrights', 'api_status', 'status']);

        if (request()->odds_api_sport_key) {
            $leagues->whereNotNull('odds_api_sport_key');
        }

        $leagues    =  $leagues->with('category')->orderBy('id', 'desc')->paginate(getPaginate());

        $categories = Category::orderBy('name')->get();
        $pageTitle = $this->pageTitle;
        return view('admin.league', compact('pageTitle', 'leagues', 'categories'));
    }

    public function index() {
        $this->pageTitle  = 'All Leagues';
        return $this->leagues();
    }

    public function inSeason() {
        $this->pageTitle  = 'In Season Leagues';
        return $this->leagues('apiActive');
    }

    public function inSeasonEnabled() {
        $this->pageTitle  = 'In Season - Enabled Leagues';
        return $this->leagues('running');
    }

    public function inSeasonDisabled() {
        $this->pageTitle  = 'In Season - Disabled Leagues';
        return $this->leagues('apiActiveAndDisabled');
    }

    public function apiEnabled() {
        $this->pageTitle  = 'API-Leagues: In Season & Enabled';
        return $this->leagues('hasApiSportKeyAndRunning');
    }

    public function manualEnabled() {
        $this->pageTitle  = 'Manual Leagues: In Season & Enabled';
        return $this->leagues('noApiSportKeyAndRunning');
    }

    public function store(Request $request, $id = 0) {

        $this->validation($request, $id);

        if ($id) {
            $league       = League::findOrFail($id);
            $notification = 'League updated successfully';
        } else {
            $league                 = new League();
            $league->manually_added = Status::YES;
            $notification           = 'League added successfully';
        }

        if ($request->hasFile('image')) {
            $fileName      = fileUploader($request->image, getFilePath('league'), getFileSize('league'), @$league->image);
            $league->image = $fileName;
        }

        $league->category_id = $request->category_id;
        $league->name        = $request->name;
        $league->short_name  = $request->short_name;
        $league->slug        = strtolower($request->slug);


        if ($league->manually_added == Status::YES) {
            $league->has_outrights = $request->has_outrights == Status::YES ? 1 : 0;
            $league->odds_api_sport_key  = $request->odds_api_sport_key;
        }

        $league->api_status = $request->api_status == Status::YES ? 1 : 0;

        $league->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return League::changeStatus($id);
    }

    public function bulkStatus($status, $ids) {
        if (!in_array($status, [Status::ENABLE, Status::DISABLE])) {
            $notify[] = ['error', 'Invalid status'];
            return back()->withNotify($notify);
        }

        League::whereIn('id', explode(',', $ids))->update(['status' => $status]);

        $notify[] = ['success', 'League status updated successfully'];
        return back()->withNotify($notify);
    }

    protected function validation($request, $id) {
        $request->validate([
            'category_id'        => 'required|exists:categories,id',
            'name'               => 'required|max:40',
            'short_name'         => 'required|max:40',
            'odds_api_sport_key' => 'nullable|unique:leagues,slug,' . $id,
            'slug'               => 'required|alpha_dash|max:255|unique:leagues,odds_api_sport_key,' . $id,
            'has_outrights'      => 'nullable|in:0,1',
            'api_status'         => 'nullable|in:0,1',
            'image'              => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
        ]);
    }
}
