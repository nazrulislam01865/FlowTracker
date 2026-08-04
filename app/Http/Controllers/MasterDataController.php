<?php
namespace App\Http\Controllers;
use Illuminate\View\View;
class MasterDataController extends Controller { public function __invoke(): View { return view('pages.master-data'); } }
