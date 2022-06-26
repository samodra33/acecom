<?php

namespace App\Http\Controllers;

use App\Models\BoardProduct;
use Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class BoardProductController extends Controller
{
    public function index()
    {
        $role = Role::find(Auth::user()->role_id);
        if ($role->hasPermissionTo('billers-index')) {
            $permissions = Role::findByName($role->name)->permissions;
            foreach ($permissions as $permission) {
                $all_permission[] = $permission->name;
            }

            if (empty($all_permission)) {
                $all_permission[] = 'dummy text';
            }

            $boardProducts = BoardProduct::all();
            return view('board-product.index', compact('boardProducts', 'all_permission'));
        } else {
            return redirect()->back()->with('not_permitted', 'Sorry! You are not allowed to access this module');
        }

    }
    public function store(Request $request)
    {
        $data = $request->all();
        BoardProduct::create($data);
        return redirect('board-product');
    }

    public function edit($id)
    {
        $boardProduct = BoardProduct::findOrFail($id);
        return $boardProduct;
    }

    public function update(Request $request, $id)
    {
        $boardProduct = BoardProduct::findOrFail($request->board_product_id);
        $boardProduct->name = $request->name;
        $boardProduct->price = $request->price;
        $boardProduct->save();
        return redirect('board-product');
    }

    public function destroy($id)
    {
        $boardProduct = BoardProduct::findOrFail($id);
        $boardProduct->delete();
        return redirect('board-product')->with('not_permitted', 'Product deleted successfully!');
    }
}
