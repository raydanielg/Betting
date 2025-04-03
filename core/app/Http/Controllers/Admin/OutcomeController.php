<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OutcomeController extends Controller {

    public function index($id) {
        $market    = Market::with('game')->findOrFail($id);
        $pageTitle = "Outcomes for - $market->title";
        $outcomes  = $market->outcomes()->latest()->withCount('bets')->paginate(getPaginate());
        return view('admin.game.outcome', compact('pageTitle', 'market', 'outcomes'));
    }

    public function store(Request $request, $id = 0) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'odds' => 'required|numeric|gt:1|regex:/^\d+(\.\d{1,2})?$/',
        ], [
            'odds.regex' => 'Only two digits are allowed as fractional number',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()]);
        }

        if ($id) {
            $outcome = Outcome::where('id', $id)->first();
            if (!$outcome) {
                return response()->json(['error' => 'Outcome not found']);
            }
            $notification = 'Outcome updated successfully';
        } else {
            $outcome            = new Outcome();
            $outcome->market_id = $request->market_id;
            $notification       = 'Outcome added successfully';
        }

        $outcome->name = $request->name;
        $outcome->odds = $request->odds;
        $outcome->save();

        $market   = $outcome->market;
        $outcomes = $market->outcomes()->withCount('bets')->get();
        return response()->json(['success' => $notification, 'outcomes' => $outcomes]);
    }

    public function status($id) {
        $outcome = Outcome::where('id', $id)->with('market')->first();
        if (!$outcome) {
            return response()->json(['success' => 'Outcome not found']);
        }
        if ($outcome->status == Status::ENABLE) {
            $outcome->status = Status::DISABLE;
        } else {
            $outcome->status = Status::ENABLE;
        }
        $outcome->save();
        $market   = $outcome->market;
        $outcomes = $market->outcomes()->withCount('bets')->get();
        return response()->json(['success' => 'Status changed successfully', 'outcomes' => $outcomes]);
    }
    public function locked($id) {
        $outcome = Outcome::where('id', $id)->with('market')->first();
        if (!$outcome) {
            return response()->json(['success' => 'Outcome not found']);
        }
        if ($outcome->locked == Status::ENABLE) {
            $outcome->locked = Status::DISABLE;
        } else {
            $outcome->locked = Status::ENABLE;
        }
        $outcome->save();
        $market   = $outcome->market;
        $outcomes = $market->outcomes()->withCount('bets')->get();
        return response()->json(['success' => 'Lock status changed successfully', 'outcomes' => $outcomes]);
    }
}
