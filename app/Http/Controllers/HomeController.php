<?php

namespace App\Http\Controllers;

use Auth;
use DB;
use Spatie\Permission\Models\Role;

/*use vendor\autoload;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;
use Mike42\Escpos\Printer;*/

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return view('home');
    }

    public function index()
    {

        $role = Role::find(Auth::user()->role_id);
        $permissions = Role::findByName($role->name)->permissions;
        foreach ($permissions as $permission) {
            $all_permission[] = $permission->name;
        }

        if (empty($all_permission)) {
            $all_permission[] = 'dummy text';
        }

		return view('index', compact('all_permission'));
    }
}