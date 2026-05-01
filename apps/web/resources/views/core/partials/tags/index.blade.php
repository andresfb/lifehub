<div>
    <ul>
    @forelse($tags as $tag)
        <li>{{ $tag->name }}</li>
    @empty
        <li><span class="text-muted">No tags</span></li>
    @endforelse
    </ul>
</div>
