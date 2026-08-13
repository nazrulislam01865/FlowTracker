<?php

namespace App\Http\Controllers;

use App\Services\MasterDataService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MasterDataController extends Controller
{
    public function __invoke(Request $request): View
    {
        $group = (string) $request->query('group', 'product');
        abort_unless(array_key_exists($group, MasterDataService::LABELS), 404);

        $module = MasterDataService::permissionModuleForType($group);
        abort_unless($request->user()?->canModule($module, 'view'), 403);

        return view('pages.master-data');
    }
}
