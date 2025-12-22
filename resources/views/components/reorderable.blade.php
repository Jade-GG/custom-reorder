<reorder v-slot="reorderSlotScope" {{ $attributes->except(['class', 'v-bind:class']) }}>
    <div {{ $attributes->only(['class', 'v-bind:class']) }}>
        {{ $slot }}
    </div>
</reorder>
