<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\OddsApi\OddsApi;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() {
        $pageTitle  = 'All Categories';
        $categories = Category::searchable(['name', 'slug'])->withCount(['teams', 'leagues'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.category', compact('pageTitle', 'categories'));
    }

    public function store(Request $request, $id = 0) {
        $request->validate([
            'name'          => 'required|max:40|unique:categories,name,' . $id,
            'odds_api_name' => 'nullable|max:40|unique:categories,odds_api_name,' . $id,
            'icon'          => 'required|max:255',
            'slug'          => 'required|alpha_dash|max:255|unique:categories,slug,' . $id,
        ], [
            'slug.alpha_dash' => 'Only alpha numeric value. No space or special character is allowed',
        ]);

        if ($id) {
            $category     = Category::findOrFail($id);
            $notification = 'Category updated successfully';
        } else {
            $category                 = new Category();
            $notification             = 'Category added successfully';
        }

        $category->name          = $request->name;
        $category->odds_api_name = $request->odds_api_name;
        $category->slug          = strtolower($request->slug);
        $category->icon          = $request->icon;
        $category->save();

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function status($id) {
        return Category::changeStatus($id);
    }

    public function fetchCategories() {
        try {
            $oddsApi = new OddsApi();
            $sports = $oddsApi->fetchSports();

            if(isset($sports['error_code'])){
                return response()->json([
                    'status'  => 'error',
                    'message' => $sports['message'],
                ]);
            }

            $sportsCollection = collect($sports)->groupBy('group');


            // Exit if no sports are returned from the API
            if ($sportsCollection->isEmpty()) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'No sports found from the API',
                ]);
            }

            // Fetch existing categories by name
            $existingCategories = Category::get()->keyBy('odds_api_name');

            $newCategories = [];

            // Iterate through each sport group to update or prepare new categories
            foreach ($sportsCollection->keys() as $sportGroup) {
                $existingCategory = $existingCategories->get($sportGroup);

                // Update existing category with missing odds_api_name
                if ($existingCategory && !$existingCategory->odds_api_name) {
                    $existingCategory->odds_api_name = $sportGroup;
                    $existingCategory->save();
                }

                // Prepare new categories if they don't exist
                if (!$existingCategory) {
                    $newCategories[] = $sportGroup;
                }
            }

            return response()->json([
                'categories' => $newCategories,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to fetch and save sports data. Please try again later.');
        }
    }

    private function prepareNewCategory($categoryName) {
        return [
            'name' => $categoryName,
            'odds_api_name' => $categoryName,
            'slug' => createUniqueSlug($categoryName, Category::class),
            'icon' => '<i class="custom-icon ' . createUniqueSlug($categoryName, Category::class) . '"></i>', // Custom icon fonts are made using SVG; follow custom-icon.css
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function saveFetchedCategories(Request $request) {

        $request->validate([
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|string'
        ], [
            'categories.required' => 'Please select at least one category to save',
        ]);

        $newCategories = [];

        foreach ($request->categories as $category) {
            $newCategories[] =$this->prepareNewCategory($category);
        }

        Category::insert($newCategories);

        $notify[] = ['success', 'Categories saved successfully'];
        return back()->withNotify($notify);
    }
}
