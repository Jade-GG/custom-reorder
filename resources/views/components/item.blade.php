
<div
    class="flex w-full"
    v-bind:class="{
        'opacity-50 !cursor-not-allowed': !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(item.product_sku),
    }"
>
    <template v-if="reorderSlotScope.canReorder(item)">
        <label
            class="flex items-center cursor-pointer pr-3"
            v-bind:class="{ '!cursor-not-allowed': !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(item.product_sku) }"
        >
            <x-rapidez::input.checkbox
                class="border-emphasis"
                type="checkbox"
                v-bind:value="item.product_sku"
                v-bind:disabled="reorderSlotScope.adding || !reorderSlotScope.matchingItems.includes(item.product_sku)"
                v-model="reorderSlotScope.selectedItems"
            />
        </label>
    </template>

    <div {{ $attributes->class('w-full') }}>
        {{ $slot }}
    </div>
</div>
