<?php
namespace App\Livewire\Dashboard;
use App\Services\DashboardService;
use Livewire\Component;
class Index extends Component { public function render(){ return view('livewire.dashboard.index', app(DashboardService::class)->data(auth()->user())); } }
