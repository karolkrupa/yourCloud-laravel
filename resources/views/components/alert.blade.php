<div class="alert alert-{{ $type or 'success' }} alert-dismissible fade show {{ $classes }}" role="alert">
    {{ $slot }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>