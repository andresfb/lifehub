@props([
    'showUserSummary' => true,
    'userEmail' => '',
    'userName' => 'User',
])

@if($showUserSummary)
    <div class="border-b border-(--lh-border) px-3.5 py-2.5">
        <div class="text-[14px] font-semibold text-(--lh-text)">{{ $userName }}</div>
        <div class="mt-0.5 text-[12px] text-(--lh-text-muted)">{{ $userEmail }}</div>
    </div>
@endif

<a href="#" class="block w-full rounded-md px-3.5 py-2 text-[13px] text-(--lh-text) no-underline hover:bg-(--lh-hover)"
>Profile</a>
<a href="#" class="block w-full rounded-md px-3.5 py-2 text-[13px] text-(--lh-text) no-underline hover:bg-(--lh-hover)"
>Settings</a>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    @method('DELETE')
    <button type="submit"
        class="block w-full cursor-pointer rounded-md border-none bg-transparent px-3.5 py-2 text-left font-[inherit] text-[13px] text-[#e54] transition-colors duration-150 hover:bg-(--lh-hover)"
    >Logout</button>
</form>
