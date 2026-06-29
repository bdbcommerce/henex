<?php
namespace App\Livewire\Admin\Users;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
class UserIndex extends Component
{
    use WithPagination;
    public function render()
    {
        $users = User::with('roles')->latest()->paginate(20);
        return view('livewire.admin.users.index', compact('users'))->layout('layouts.admin');
    }
}
