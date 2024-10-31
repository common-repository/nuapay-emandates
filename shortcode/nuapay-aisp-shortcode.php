<?php

$nuapay_aisp_shortcode = new NuapayAispShortcode();

class NuapayAispShortcode extends Shortcode
{
    const SIGN_BUTTON_ID = "np-sign-aisp-btn";
    const SIGN_REDIRECT_BUTTON_ID = "np-sign-aisp-redirect-btn";

    public function __construct()
    {
        add_shortcode('nuapay-aisp', array($this, 'np_form_creation_aisp'));
    }

    public function np_form_creation_aisp($attrs)
    {
        $attributes = $this->parse_nuapay_attributes($attrs);

        if (isset($missing_args) && ($missing_args === true)) {
            $this->render_wrong_configuration_message();
        } else if (in_array($attributes['integration_type'], array('REDIRECT', 'OVERLAY')) === false) {
            $this->render_not_supported_integration_message($attributes['integration_type']);
        } else {
            $options = get_option('np_form_options');

            $integration_type = $attributes['integration_type'];
            $api_key = $attributes['api_key'];

            $is_overlay = strcmp($integration_type, 'OVERLAY') == 0;

            $rest_url = trim($options[NPSettings::REST_URL], '/');
            $web_url = $rest_url . '/tpp-ui';

            $token_response = $this->fetch_access_token($rest_url, $api_key, $attributes);

            $token_response_code = wp_remote_retrieve_response_code($token_response);

            $response = json_decode(wp_remote_retrieve_body($token_response));

            if ($this->is_valid_token_response($token_response_code, $response)) {
                $token = $response->data->token;

                $this->attach_jQuery($web_url);

                wp_enqueue_script('np-js-ui', NUAPAY_PLUGIN_URL . 'overlay/ui.js');
                wp_enqueue_script('np-js-open-banking-api', $web_url . '/js/nuapay-open-banking.js');
                wp_enqueue_style('np-css-open-banking-theme', $web_url . '/css/nuapay-open-banking.css');

                wp_localize_script('np-js-ui', 'NP_Script_Params', array(
                    'authToken' => $token,
                    'webUrl' => $web_url
                ));

                if ($is_overlay) {
                    return $this->get_sign_button(self::SIGN_BUTTON_ID);
                } else {
                    return $this->get_sign_redirect_button(self::SIGN_REDIRECT_BUTTON_ID);
                }
            } else {
                $this->render_authentication_error_message($response, $token_response);
            }
        }
    }
}