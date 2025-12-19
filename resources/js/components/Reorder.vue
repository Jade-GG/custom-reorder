<script>
import { mask as cartMask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

export default {
    props: {
        items: Array,
    },
    render() {
        return this.$scopedSlots.default(this)
    },

    data() {
        return {
            reordering: false,
            selectedItems: [],
            matchingItems: [],
            unconfiguredItems: [],
            adding: false,
            added: false,
        }
    },

    mounted() {
        this.$root.$on('reordering', () => {
            this.reordering = true
            // By default, select every selectable item
            this.selectedItems = [...this.matchingItems]
        })

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
            if (filters.some((filter) => item.product_sku.match(new RegExp(filter)))) {
                return false
            }

            return true
        },

        async getMatchingProducts() {
            let skus = this.items.map(item => item.product_sku)
            let response = await window.magentoGraphQL(
                `query Products {
                    products(
                        filter: { sku: { in: [${skus.join(',')}] } }
                        pageSize: 999
                        currentPage: 1
                    ) {
                        items {
                            sku
                            options
                            __typename
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
        },

        isUnconfigured(currentItem) {
            if (!currentItem.options && currentItem.__typename !== 'ConfigurableProduct') {
                return false
            }

            let item = this.items.find(item => item.product_sku == currentItem.sku)
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
                            let item = this.items.find(item => item.product_sku == sku)
                            return {
                                ...item,
                                'sku': item.product_sku,
                                'quantity': item.quantity_ordered,
                            }
                        }),
                    },
                )
                await this.updateCart({}, response)

                Notify(window.config.translations.cart.add_selected, 'success', [], window.url('/cart'))
                this.added = true
                setTimeout(() => this.added = false, 3000)
            } catch(error) {
                Notify(error.message, 'error')
            }

            this.adding = false
        },
    },
}
</script>
