<?php

namespace App\Livewire\Admin\Telegram;

use App\Models\Industry;
use App\Models\TelegramUser;
use App\Telegram\BotSetup;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TelegramIndex extends Component
{
    use WithPagination;

    #[Url] public string $search = '';
    #[Url] public string $industry = '';
    #[Url] public string $status = 'registered';

    public ?array $botStatus = null;
    public array $setupResult = [];

    public function updating($name): void
    {
        if (in_array($name, ['search', 'industry', 'status'], true)) {
            $this->resetPage();
        }
    }

    public function loadStatus(BotSetup $setup): void
    {
        $this->botStatus = $setup->status();
    }

    public function connect(BotSetup $setup): void
    {
        abort_unless(auth()->user()->hasRole('super_admin'), 403);

        $this->setupResult = $setup->run();
        $this->botStatus = $setup->status();

        $failed = array_filter($this->setupResult, fn ($ok) => $ok !== true);
        $this->dispatch('toast',
            message: $failed ? 'Some steps failed — see details.' : 'Bot connected: webhook, commands and menu button set.',
            type: $failed ? 'error' : 'success');
    }

    public function render()
    {
        $users = TelegramUser::query()
            ->with('industry')->withCount('inquiries')
            ->when($this->status === 'registered', fn ($q) => $q->whereNotNull('registered_at'))
            ->when($this->status === 'unfinished', fn ($q) => $q->whereNull('registered_at'))
            ->when($this->status === 'blocked', fn ($q) => $q->where('is_blocked', true))
            ->when($this->industry, fn ($q) => $q->where('industry_id', $this->industry))
            ->when($this->search, fn ($q) => $q->where(fn ($q) => $q
                ->where('full_name', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
                ->orWhere('username', 'like', "%{$this->search}%")))
            ->latest('last_seen_at')
            ->paginate(25);

        $stats = [
            'registered' => TelegramUser::whereNotNull('registered_at')->count(),
            'unfinished' => TelegramUser::whereNull('registered_at')->count(),
            'blocked'    => TelegramUser::where('is_blocked', true)->count(),
            'active_7d'  => TelegramUser::where('last_seen_at', '>=', now()->subDays(7))->count(),
        ];

        $industries = Industry::ordered()->get();
        $aiEnabled = filled(config('services.ai.api_key'));

        return view('livewire.admin.telegram.index', compact('users', 'stats', 'industries', 'aiEnabled'))->layout('layouts.admin');
    }
}
