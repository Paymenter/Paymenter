<div id="paypal-button-container"></div>

@script
    <script>
        const script = document.createElement("script");
        script.src =
            "https://www.paypal.com/sdk/js?client-id={{ $clientId }}&currency={{ $invoice->currency_code }}&components=buttons{!! isset($plan) ? '&intent=subscription&vault=true' : '' !!}";
        script.async = true;
        document.body.appendChild(script);

        script.onload = () => {
            // Render the PayPal button
            paypal
                .Buttons({
                    style: {
                        shape: "rect",
                        layout: "vertical",
                        color: "gold",
                        label: "paypal",
                    },
                    message: {
                        amount: {{ $total }},
                    },
                    {!! isset($order) ? 'async createOrder(){return \'' . $order->id . '\';},' : '' !!}
                    {!! isset($plan)
                        ? 'async createSubscription(data, actions){return actions.subscription.create({plan_id:\'' .
                            $plan->id .
                            '\',custom_id:\'' .
                            $invoice->id .
                            '\',application_context:{user_action:\'SUBSCRIBE_NOW\',payment_method:{payer_selected:\'PAYPAL\',payee_preferred:\'IMMEDIATE_PAYMENT_REQUIRED\'}},subscriber:{name:{given_name:\'' .
                            auth()->user()->name .
                            '\'},email_address:\'' .
                            auth()->user()->email .
                            '\'}});},'
                        : '' !!}

                    async onApprove(data, actions) {
                        try {
                            // If plan is set just redirect to the invoice page
                            {!! isset($plan)
                                ? 'return actions.redirect(\'' . route('invoices.show', $invoice) . '?checkPayment=true\');'
                                : '' !!}

                            const response = await fetch(
                                `{{ route('extensions.gateways.paypal.capture') }}?orderID=${data.orderID}`, {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                    },
                                });

                            const orderData = await response.json();


                            const errorDetail = orderData?.details?.[0];

                            if (errorDetail?.issue === "INSTRUMENT_DECLINED") {
                                return actions.restart();
                            } else if (errorDetail) {
                                throw new Error(`${errorDetail.description} (${orderData.debug_id})`);
                            } else if (!orderData.purchase_units) {
                                throw new Error(JSON.stringify(orderData));
                            } else {
                                actions.redirect('{{ route('invoices.show', $invoice) }}?checkPayment=true');
                            }
                        } catch (error) {
                            console.error(error);
                            resultMessage(
                                `Sorry, your transaction could not be processed...<br><br>${error}`
                            );
                        }
                    },
                }).render("#paypal-button-container")
        };
    </script>
@endscript
