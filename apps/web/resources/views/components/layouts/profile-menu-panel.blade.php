@props([
    'showUserSummary' => true,
    'userEmail' => '',
    'userName' => 'User',
])

@if($showUserSummary)
    <div class="border-b border-base-300 px-4 py-3">
        <div class="text-sm font-semibold text-base-content">{{ $userName }}</div>
        <div class="mt-0.5 text-xs text-base-content/70">{{ $userEmail }}</div>
    </div>
@endif

<ul class="menu menu-sm w-full gap-1 p-2">
    <li><a href="#">Profile</a></li>
    <li><a href="#">Settings</a></li>
</ul>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    @method('DELETE')
    <div class="px-2 pb-2">
        <button type="submit" class="btn btn-error btn-soft btn-sm w-full justify-start">Logout</button>
    </div>
</form>
