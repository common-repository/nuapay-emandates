<?php

$nuapay_pisp_shorcode = new NuapayPispShortcode();

class NuapayPispShortcode extends Shortcode
{
    const SIGN_BUTTON_ID = "np-sign-pisp-btn";
    const SIGN_REDIRECT_BUTTON_ID = "np-sign-pisp-redirect-btn";

    const AMOUNT_DEFAULT = 0.01;
    const CURRENCY_DEFAULT = 'GBP';
    const COUNTRY_CODE_DEFAULT = 'GB';

    public function __construct()
    {
        add_shortcode('nuapay-pisp', array($this, 'np_form_creation_pisp'));
    }

    public function np_form_creation_pisp($attrs)
    {
        $attributes = $this->parse_nuapay_pisp_attributes($attrs);
        $missing_args = check($attributes);

        if (isset($missing_args) && ($missing_args === true)) {
            $this->render_wrong_configuration_message();
        } else if (in_array($attributes['integration_type'], array('REDIRECT', 'OVERLAY')) === false) {
            $this->render_not_supported_integration_message($attributes['integration_type']);
        } else {
            $options = get_option('np_form_options');

            $integration_type = $attributes['integration_type'];
            $isOverlay = strcmp($integration_type, 'OVERLAY') == 0;

            $api_key = $attributes['api_key'];
            $merchant_post_auth_url = $attributes['merchant_post_auth_url'];

            $rest_url = trim($options[NPSettings::REST_URL], '/');
            $web_url = $rest_url . '/tpp-ui';

            $creditor_scheme_id = $attributes['scheme_id'];

            $amount = array_key_exists('amount', $attributes) ? $attributes['amount'] : self::AMOUNT_DEFAULT;
            $currency = array_key_exists('currency', $attributes) ? $attributes['currency'] : self::CURRENCY_DEFAULT;
            $country_code = array_key_exists('country_code', $attributes) ? $attributes['country_code'] : self::COUNTRY_CODE_DEFAULT;

            $post_payment_response = $this->fetch_user_interface_payment_id($rest_url, $api_key, $amount, $currency,
                $country_code, $merchant_post_auth_url);
            $post_payment_response_code = wp_remote_retrieve_response_code($post_payment_response);
            $post_payment_response_body = json_decode(wp_remote_retrieve_body($post_payment_response));

            if ($post_payment_response_code == 201 && !is_null($post_payment_response_body->data)
                && !is_null($post_payment_response_body->data->userInterfacePaymentId)) {

                $user_interface_payment_id = $post_payment_response_body->data->userInterfacePaymentId;

                $this->attach_jQuery($web_url);

                wp_enqueue_script('np-js-ui', NUAPAY_PLUGIN_URL . 'overlay/ui.js');
                wp_enqueue_script('np-js-open-banking-api', $web_url . '/js/nuapay-open-banking.js');
                wp_enqueue_style('np-css-open-banking-theme', $web_url . '/css/nuapay-open-banking.css');

                wp_localize_script('np-js-ui', 'NP_Script_Params', array(
                    'currency' => $currency,
                    'paymentUiid' => $user_interface_payment_id,
                    'csid' => $creditor_scheme_id,
                    'webUrl' => $web_url
                ));

                if ($isOverlay) {
                    return $this->get_sign_button(self::SIGN_BUTTON_ID);
                } else {
                    return $this->get_sign_redirect_button(self::SIGN_REDIRECT_BUTTON_ID);
                }
            } else {
                $this->render_user_interface_payment_id_failed_error();
            }
        }
    }

    private function render_user_interface_payment_id_failed_error()
    {
        NPUtils::render('error', array(
            'message' => NPUtils::i18('Cannot obtain user interface payment id.')
        ));
    }

    private function parse_nuapay_pisp_attributes($attrs)
    {
        return shortcode_atts(array(
            'integration_type' => null,
            'api_key' => null,
            'scheme_id' => null,
            // country_code           - Optional
            // amount                 - Optional
            // merchant_post_auth_url - Optional
        ), $attrs);
    }

    private function fetch_user_interface_payment_id($url, $api_key, $amount, $currency, $country_code,
                                                     $merchant_post_auth_url)
    {
        $post_payment_request = array(
            'amount' => $amount,
            'currency' => $currency,
            'countryCode' => $country_code,
            'merchantPostAuthUrl' => $merchant_post_auth_url,
            'remittanceInformation' => array(
                'reference' => uniqid()
            )
        );

        return wp_remote_post($url . '/tpp/payments', array(
            'method' => 'POST',
            'timeout' => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($api_key . ':')
            ),
            'body' => json_encode($post_payment_request),
            'cookies' => array()
        ));
    }
}


