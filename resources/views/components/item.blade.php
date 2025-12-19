<label
    class="flex gap-3"
    v-bind:class="{
        'cursor-pointer': reorderSlotScope.reordering,
        'opacity-50 !cursor-not-allowed': reorderSlotScope.reordering && !reorderSlotScope.matchingItems.includes(item.product_sku),
    }"
>
    <template v-if="reorderSlotScope.reordering && reorderSlotScope.canReorder(item)">
        <div class="flex items-center">
            <x-rapidez::input.checkbox
                type="checkbox"
                v-bind:value="item.product_sku"
                v-bind:disabled="reorderSlotScope.adding || !reorderSlotScope.matchingItems.includes(item.product_sku)"
                v-model="reorderSlotScope.selectedItems"
            />
        </div>
    </template>

    <div {{ $attributes }}>
        {{ $slot }}
    </div>
</label>
