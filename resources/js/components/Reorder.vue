<script>
import { mask as cartMask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

export default {
    props: {
        items: Array,
        cartItems: {
            type: Boolean,
            default: false,
        }
    },
    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            loading: true,
            selectedItems: [],
            matchingItems: [],
            unconfiguredItems: [],
            adding: false,
            added: false,
        }
    },

    mounted() {
        if (this.items) {
            this.getMatchingProducts()
        }
    },

    watch: {
        items() {
            this.getMatchingProducts()
        }
    },

    methods: {
        canReorder(item) {
            let filters = window.config?.custom_reorder?.sku_filters ?? []
            if (filters.some((filter) => this.transformItem(item).sku.match(new RegExp(filter)))) {
                return false
            }

            return true
        },

        async getMatchingProducts() {
            let skus = this.transformedItems.map(item => item.sku)
            let response = await window.magentoGraphQL(
                `query Products {
                    products(
                        filter: { sku: { in: [${skus.map(sku => `"${sku}"`).join(',')}] } }
                        pageSize: 999
                        currentPage: 1
                    ) {
                        items {
                            sku
                            __typename
                            ... on VirtualProduct { options { uid } }
                            ... on SimpleProduct { options { uid } }
                            ... on ConfigurableProduct { options { uid } }
                            ... on BundleProduct { options { uid } }
                            ... on DownloadableProduct { options { uid } }
                        }
                    }
                }`
            )
            this.matchingItems = (response.data.products.items ?? [])
                .filter(item => !this.isUnconfigured(item))
                .map(item => item.sku)

            this.unconfiguredItems = (response.data.products.items ?? [])
                .filter(item => this.isUnconfigured(item))
                .map(item => item.sku)

            this.loading = false
        },

        isUnconfigured(currentItem) {
            if (!currentItem.options && currentItem.__typename !== 'ConfigurableProduct') {
                return false
            }

            let item = this.transformedItems.find(item => item.sku == currentItem.sku)
            if (item && (item.entered_options || item.selected_options)) {
                return false
            }

            return true
        },

        async addToCart() {
            if (!this.selectedItems.length) {
                return
            }
            this.adding = true

            try {
                let response = await window.magentoGraphQL(
                    `mutation ($cartId: String!, $cartItems: [CartItemInput!]!) {
                        addProductsToCart(cartId: $cartId, cartItems: $cartItems)
                        { cart { ...cart } user_errors { code message } }
                    }
                    ` + config.fragments.cart,
                    {
                        cartId: cartMask.value,
                        cartItems: this.selectedItems.map((sku) => {
                            let item = this.transformedItems.find(item => item.sku == sku)
                            return item
                        }),
                    },
                )
                await this.updateCart({}, response)

                Notify(window.config.custom_reorder.add_selected, 'success', [], window.url('/cart'))
                this.added = true
                setTimeout(() => this.added = false, 3000)
            } catch(error) {
                Notify(error.message, 'error')
            }

            this.adding = false
        },

        transformItem(item) {
            if (!item) {
                return null
            }

            // Only transform when the item is in OrderItem format
            if (this.cartItems) {
                return item
            }

            // Skip if already transformed
            if ('sku' in item) {
                return item
            }

            // TODO: Support selected_options and entered_options from quote item data
            return {
                quantity: item.quantity_ordered,
                sku: item.product_sku,
            }
        },
    },

    computed: {
        transformedItems() {
            return this.items.map(this.transformItem)
        },
    },
}
</script>
