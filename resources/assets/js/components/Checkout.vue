<template>
    <div :class="root_class">
        <div class="ConcertCheckout__price">
            <label class="ConcertCheckout__label Label">Price</label>
            <div class="CheckoutPrice__amount">&pound;{{ total_price_in_gbp }}</div>
        </div>
        <div class="ConcertCheckout__quantity">
            <label class="ConcertCheckout__label Label" for="quantity">Quantity</label>
            <input type="number" class="Input Input--text" id="quantity"
                   v-model="quantity"
                   min="1"
                   :max="tickets_remaining"
                   step="1">
        </div>

        <button class="ConcertCheckout__button"
                v-on:click="openStripe">
            Buy Tickets
        </button>
    </div>
</template>


<script>
    import axios from 'axios';

    export default {
        props: ['root_class', 'concert_id', 'ticket_price', 'tickets_remaining'],

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
                    zipCode: false,
                    currency: 'gbp',
                    amount: this.quantity * this.ticket_price
                })
            },

            charge: function(token) {
                const payload = {
                    email: token.email,
                    ticket_quantity: this.quantity,
                    payment_token: token.id
                };

                axios.post(`/concerts/${this.concert_id}/orders`, payload)
                    .then(res => {
                        window.location = '/orders/' + res.data.confirmation_number;
                    });
            }
        }
    }
</script>
