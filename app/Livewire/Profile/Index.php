<?php
namespace App\Livewire\Profile;

use App\Livewire\Concerns\UsesPagePlaceholder;
use App\Services\ProfileService;use Livewire\Component;
class Index extends Component
{
    use UsesPagePlaceholder;
    public string $name='';public string $email='';public string $locale='en';public string $currentPassword='';public string $newPassword='';public string $newPasswordConfirmation='';
    public function mount():void{$u=auth()->user();$this->name=$u->name;$this->email=$u->email;$this->locale=$u->locale;}
    public function saveProfile():void{$d=$this->validate(['name'=>['required','string','max:255'],'email'=>['required','email','unique:users,email,'.auth()->id()],'locale'=>['required','in:en,zh']]);app(ProfileService::class)->update(auth()->user(),$d);session()->flash('success','Profile updated.');}
    public function changePassword():void{$this->validate(['currentPassword'=>['required','string'],'newPassword'=>['required','string','min:10','same:newPasswordConfirmation']]);if(!app(ProfileService::class)->changePassword(auth()->user(),$this->currentPassword,$this->newPassword)){$this->addError('currentPassword','Current password is incorrect.');return;}$this->reset(['currentPassword','newPassword','newPasswordConfirmation']);session()->flash('success','Password updated.');}
    public function render(){return view('livewire.profile.index');}
}
