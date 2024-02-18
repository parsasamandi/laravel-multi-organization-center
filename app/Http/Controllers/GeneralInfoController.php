<?php

namespace App\Http\Controllers;

use App\DataTables\CategoryDataTable;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Http\Requests\StoreCategoryRequest;
use App\Providers\Action;
use App\Models\Category;
use App\Models\Subcategory;
use DB;

class GeneralInfoController extends Controller
{
    // Datatable to blade
    public function list(Request $request) {

        $dataTable = new CategoryDataTable;

        $vars['categoryTable'] = $dataTable->html();

        return view('category.categoryList', $vars);
    }

    // Rendering DataTable
    public function categoryTable(CategoryDataTable $dataTable) {
        return $dataTable->render('category.categoryList');
    }

    // Store
    public function store(StoreCategoryRequest $request) {

        DB::transaction(function() use($request) {

            $id = $request->get('id');

            // Insert or update
            $category = Category::updateOrCreate(
                ['id' => $id],
                ['name' => $request->get('name')]
            );  

            // Status
            $category->statuses()->updateOrCreate(
                ['status_id' => $id],
                ['status' => $request->get('status'), 'status_type' => Category::class]
            );

        });

        return $this->getAction($request->get('button_action'));
    }

    // Edit
    public function edit(Action $action, Request $request) {
        return $action->edit(Category::class, $request->get('id'));
    }

    // Delete
    public function delete(Action $action, $id) {
        return $action->delete(Category::class, $id);
    }

    // Subcategories to be filled based on categories(ajax)
    public function ajax_subCategory(Request $request) {

        $category_id = $request->get('category_id');
        $subcategories = Subcategory::where('category_id', $category_id)->get();

        return response()->json($subcategories);
    }
    
}
