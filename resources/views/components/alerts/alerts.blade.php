
@props(['type'])

@if (session()->has($type))
    <div {{ $attributes->merge(['class' => 'alert alert-' . $type]) }} role="alert">

        <div class="alert-body">{{ session($type) }}</div>
    </div>

    <div class="alert alert-primary" role="alert">

    </div>
@endif
