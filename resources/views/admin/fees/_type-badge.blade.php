@if($type === 'term')
    <span class="badge badge-active">Term</span>
@elseif($type === 'ongoing')
    <span class="badge badge-pending">Monthly</span>
@else
    <span class="badge">Private</span>
@endif
