<form id="payment-form" class="mt-2 text-white">
    <div id="error-message" class="hidden text-red-500 mb-2">

    </div>
    <div id="payment-element">

    </div>
    <button id="submit"
        class="mt-4 bg-secondary-500 text-white hover:bg-secondary py-2 px-4 rounded-md w-full bg-gradient-to-tr from-secondary via-50% via-20% via-secondary to-[#5573FD80] duration-300">
        Pay
    </button>
</form>
@script
<script>
    const script = document.createElement('script');
        script.src = 'https://js.stripe.com/v3/';
        script.async = true;
        document.body.appendChild(script);
        script.onload = () => {

            var stripe = Stripe(
                "{{ $stripePublishableKey }}"
            );

            const options = {
                clientSecret: '{{ $intent->client_secret }}',
                appearance: {
                    theme: 'night'
                },
            };

            const paymentElementOptions = {
                layout: {
                    type: 'accordion',
                    defaultCollapsed: false,
                    radios: false,
                    spacedAccordionItems: true
                },
                defaultValues: {
                    billingDetails: {
                        name: "{{ auth()->user()->name }}",
                        email: "{{ auth()->user()->email }}",
                    }
                }
            }

            // Set up Stripe.js and Elements to use in checkout form, passing the client secret obtained in a previous step
            const elements = stripe.elements(options);
            const paymentElement = elements.create('payment', paymentElementOptions);
            paymentElement.mount('#payment-element');

            const form = document.getElementById('payment-form');

            form.addEventListener('submit', async (event) => {
                event.preventDefault();
                setLoading(true);
                
                var error;

                var {
                    error
                } = await stripe.confirmPayment({
                    //`Elements` instance that was used to create the Payment Element
                    elements,
                    confirmParams: {
                        return_url: '{{ route('invoices.show', $invoice) }}?checkPayment=true',
                    },
                });


                // This point will only be reached if there is an immediate error when
                // confirming the payment. Otherwise, your customer will be redirected to
                // your `return_url`. For some payment methods like iDEAL, your customer will
                // be redirected to an intermediate site first to authorize the payment, then
                // redirected to the `return_url`.
                if (error.type === "card_error" || error.type === "validation_error") {
                    showMessage(error.message);
                } else {
                    showMessage("An unexpected error occurred.");
                }

                setLoading(false);
            });

            function showMessage(messageText) {
                const messageContainer = document.querySelector("#error-message");

                messageContainer.classList.remove("hidden");
                messageContainer.textContent = messageText;

                setTimeout(function() {
                    messageContainer.classList.add("hidden");
                    messageContainer.textContent = "";
                }, 4000);
            }

            // Show a spinner on payment submission
            function setLoading(isLoading) {
                if (isLoading) {
                    // Disable the button and show a spinner
                    document.querySelector("#submit").disabled = true;
                    document.querySelector("#submit").classList.add("hidden");
                } else {
                    document.querySelector("#submit").disabled = false;
                    document.querySelector("#submit").classList.remove("hidden");
                }
            }

            $wire.on('invoice.payment.cancelled', () => {
                // destroy the payment element
                paymentElement.destroy();
            });
        };
</script>
@endscript