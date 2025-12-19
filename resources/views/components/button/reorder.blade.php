@props(['component' => 'rapidez::button.primary'])

<toggler v-slot="{ toggle, isOpen }">
    <x-dynamic-component
        :$component
        v-if="!isOpen"
        v-cloak
        v-bind:disabled="$root.loading"
        v-on:click="$root.$emit('reordering', true); toggle()"
    >
        {{ $slot }}
    </x-dynamic-component>
</toggler>
