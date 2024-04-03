<x-app-layout>
    <x-center>
        <script src="https://js.stripe.com/v3/"></script>
        @php
            $stripe = new \Stripe\StripeClient(
                'sk_test_51Lb11lAe4mynQ6gLpfUBkUfr5N9dsSZTS8KLjhoNTIGkRLJybrBBMOf6HBDVFQkIuLMwozRriSZS8s469Q9yq0Hj00ll73KdrX',
            );

            $paymentIntent = $stripe->paymentIntents->create([
                'amount' => 1099,
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
            ]);
        @endphp
        <form id="payment-form" class="mt-2 text-white">
            <h1 class="text-2xl font-bold text-center">Payment for invoice #123</h1>
            <p class="text-center mb-2">Amount: €10.99</p>
            <div id="payment-element">
                <!-- Elements will create form elements here -->
            </div>
            <x-button.primary id="submit">Submit</x-button.primary>
            <div id="error-message">
                <!-- Display error message to your customers here -->
            </div>
        </form>
        <script>
            var stripe = Stripe(
                'pk_test_51Lb11lAe4mynQ6gLBaQAwUfeJWroIvsZprKOaSXm3Sg4G99PLTgwGOk1LYSf8Sg6chkn8a1k6beM3Ie2bMsNeSd000WZkJckGQ'
            );

            const options = {
                clientSecret: '{{ $paymentIntent->client_secret }}',
                appearance: {
                    theme: 'night'

                },
            };

            // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in a previous step
            const elements = stripe.elements(options);
            const paymentElement = elements.create('payment');
            paymentElement.mount('#payment-element');

            const form = document.getElementById('payment-form');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();

                const {
                    error
                } = await stripe.confirmPayment({
                    //`Elements` instance that was used to create the Payment Element
                    elements,
                    confirmParams: {
                        return_url: 'https://example.com/order/123/complete',
                    },
                });

                if (error) {
                    // This point will only be reached if there is an immediate error when
                    // confirming the payment. Show error to your customer (for example, payment
                    // details incomplete)
                    const messageContainer = document.querySelector('#error-message');
                    messageContainer.textContent = error.message;
                } else {
                    // Your customer will be redirected to your `return_url`. For some payment
                    // methods like iDEAL, your customer will be redirected to an intermediate
                    // site first to authorize the payment, then redirected to the `return_url`.
                }
            });
        </script>
    </x-center>

</x-app-layout>
