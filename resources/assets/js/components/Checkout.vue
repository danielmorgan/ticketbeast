<template>
    <div class="Checkout">
        <div class="CheckoutPrice">
            <div class="Checkout__label">Price</div>
            <div class="CheckoutPrice__amount">&pound;{{ total_price_in_gbp }}</div>
        </div>
        <div class="CheckoutQuantity">
            <div class="Checkout__label">Quantity</div>
            <input class="CheckoutQuantity__input" type="text" v-model="quantity">
        </div>

        <button class="CheckoutButton"
                v-on:click="openStripe">
            Buy Tickets
        </button>
    </div>
</template>


<script>
    export default {
        props: ['ticket_price'],

        data() {
            return {
                quantity: 1,
                handler: null
            };
        },

        computed: {
            total_price_in_gbp: function() {
                return ((this.ticket_price * this.quantity) / 100).toFixed(2);
            }
        },

        mounted() {
            this.handler = StripeCheckout.configure({
                key: window.Laravel.stripeKey,
                locale: 'auto',
                token: this.charge
            });
        },

        methods: {
            openStripe: function() {
                this.handler.open({
                    name: 'TicketBeast',
                    description: this.quantity + ' tickets',
                    zipCode: true,
                    currency: 'gbp',
                    amount: this.quantity * this.ticket_price
                })
            },

            charge: function(token) {
                console.log('charge', token);
            }
        }
    }
</script>
