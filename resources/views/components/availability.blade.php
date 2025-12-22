<div v-if="reorderSlotScope.canReorder(item) && !reorderSlotScope.loading && !reorderSlotScope.matchingItems.includes(item.product_sku)">
    <template v-if="reorderSlotScope.unconfiguredItems.includes(item.product_sku)">
        (@lang('Product needs to be configured'))
    </template>
    <template v-else>
        (@lang('Product not available'))
    </template>
</div>
