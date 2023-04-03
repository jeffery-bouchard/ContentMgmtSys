<?php

    use Square\Environment;
    use Square\Models\Money;
    use Square\SquareClient;
    use Square\Models\QuickPay;
    use Square\Exceptions\ApiException;
    use Square\Models\CreatePaymentLinkRequest;

    class Checkout {
        
        private $location;
        private $client;
        private $redirect_url;

        // constructor
        function __construct(string $redirect_url) {
            $this->location = getenv("SQRLOC");
            $config = [
                'accessToken' => getenv("XSTOKEN"),
                'environment' => 'production'
            ];

            $this->client = new SquareClient($config);
            $this->redirect_url = $redirect_url;
        }

        // single item checkout (buy now)
        public function singleItemCheckout(array $item): string {

            $price_money = new \Square\Models\Money();
            $price_money->setAmount($item['price']);
            $price_money->setCurrency('USD');
    
            $quick_pay = new \Square\Models\QuickPay(
                $item['name'],
                $price_money,
                $this->location
            );
    
            $checkout_options = new \Square\Models\CheckoutOptions();
            $checkout_options->setAllowTipping(false);
            $checkout_options->setRedirectUrl($this->redirect_url);
            $checkout_options->setAskForShippingAddress(false);
    
            $body = new \Square\Models\CreatePaymentLinkRequest();
            $body->setQuickPay($quick_pay);
            $body->setCheckoutOptions($checkout_options);
    
            $api_response = $this->client->getCheckoutApi()->createPaymentLink($body);

            if ($api_response->isSuccess()) {
                $result = $api_response->getResult();
                $paymentLink = $result->getPaymentLink();
                return $paymentLink->getURL();
            } else {
                $errors = $api_response->getErrors();
                error_log($errors);
                return null;
            }
        }

        // multi-item checkout (cart checkout)
        public function multiItemCheckout(array $items): string {

            $line_items = [];

            foreach ($items as $item) {
                $base_price_money = new \Square\Models\Money();
                $base_price_money->setAmount($item['price']);
                $base_price_money->setCurrency('USD');

                $order_line_item = new \Square\Models\OrderLineItem('1');
                $order_line_item->setName($item['name']);
                $order_line_item->setBasePriceMoney($base_price_money);

                array_push($line_items, $order_line_item);
            }

            $order = new \Square\Models\Order($this->location);
            $order->setLineItems($line_items);

            $checkout_options = new \Square\Models\CheckoutOptions();
            $checkout_options->setAllowTipping(false);
            $checkout_options->setRedirectUrl($this->redirect_url);
            $checkout_options->setAskForShippingAddress(false);

            $body = new \Square\Models\CreatePaymentLinkRequest();
            $body->setIdempotencyKey(uniqid('', true));
            $body->setOrder($order);
            $body->setCheckoutOptions($checkout_options);

            $api_response = $this->client->getCheckoutApi()->createPaymentLink($body);

            if ($api_response->isSuccess()) {
                $result = $api_response->getResult();
                $paymentLink = $result->getPaymentLink();
                return $paymentLink->getURL();
            } else {
                $errors = $api_response->getErrors();
                error_log($errors);
                return null;
            }
        }

    }
?>