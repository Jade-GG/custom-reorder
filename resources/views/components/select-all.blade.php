@props(['component' => 'rapidez::button.primary'])

<x-dynamic-component
    :$component
    v-bind:disabled="$root.loading"
    v-on:click="$root.$emit('reorder-all', true)"
>
    {{ $slot }}
</x-dynamic-component>
