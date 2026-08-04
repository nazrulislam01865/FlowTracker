<?php
namespace App\Http\Controllers;
use Illuminate\View\View;
class ProfileController extends Controller { public function __invoke(): View { return view('pages.profile'); } }
