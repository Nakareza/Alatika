@props([
    'data'
])

@if($data->hasPages())

<div class="mt-6">

    {{ $data->links('vendor.pagination.alatika') }}

</div>

@endif