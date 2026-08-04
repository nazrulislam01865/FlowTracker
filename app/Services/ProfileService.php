<?php
namespace App\Services;
use App\Models\User;use Illuminate\Support\Facades\Hash;
class ProfileService { public function update(User $user,array $data):User{$user->update($data);return $user->refresh();} public function changePassword(User $user,string $current,string $new):bool{if(!Hash::check($current,$user->password))return false;$user->update(['password'=>$new]);return true;} }
