@if($terms->isNotEmpty())
    <ul class="menu menu-sm w-full rounded-box border border-base-300 bg-base-100 shadow-lg">
    @foreach($terms as $item)
        <li>
            <button type="button" data-term="{{ $item->term }}" class="search-suggestion w-full text-left">
                {{ $item->term }}
            </button>
        </li>
    @endforeach
    </ul>
@endif
