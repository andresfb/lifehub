<div>
    <div class="flex flex-wrap gap-2">
    @forelse($tags as $tag)
        <span class="badge badge-soft badge-primary">{{ $tag->name }}</span>
    @empty
        <span class="text-sm text-base-content/60">No tags</span>
    @endforelse
    </div>
</div>
