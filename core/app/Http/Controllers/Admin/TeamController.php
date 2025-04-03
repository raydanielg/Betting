<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Team;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;

class TeamController extends Controller {
    public function index() {
        $pageTitle  = 'All Teams';
        $teams      = Team::searchable(['name', 'slug', 'category:name'])->latest()->with('category')->paginate(getPaginate());
        $categories = Category::latest()->get();
        return view('admin.team', compact('pageTitle', 'teams', 'categories'));
    }

    public function store(Request $request, $id = 0) {

        $this->validation($request, $id);

        if ($id) {
            $team         = Team::findOrFail($id);
            $notification = 'Team updated successfully';
        } else {
            $team         = new Team();
            $notification = 'Team added successfully';
            $team->manually_added = Status::YES;
        }

        if ($request->hasFile('image')) {
            $fileName    = fileUploader($request->image, getFilePath('team'), getFileSize('team'), @$team->image);
            $team->image = $fileName;
        }

        $team->category_id = $request->category_id;
        $team->name        = $request->name;
        $team->short_name  = $request->short_name;
        $team->slug        = strtolower($request->slug);
        $team->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    protected function validation($request, $id) {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name'        => 'required|max:255',
            'short_name'  => 'required|max:40',
            'slug'        => 'required|alpha_dash|max:255|unique:teams,slug,' . $id,
            'image'       => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
        ]);
    }
}
