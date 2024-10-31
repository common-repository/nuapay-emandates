jQuery(document).ready(function () {

    if (jQuery('#np-sign-btn').length || jQuery('#np-sign-redirect-btn').length) {
        EMandates.setToken(NP_Script_Params.authToken);
        EMandates.setUrl(NP_Script_Params.emandatesWebUrl);
        EMandates.registerOverlayListener();

        jQuery('#np-sign-btn').click(function () {
            EMandates.overlay();
        });

        jQuery('#np-sign-redirect-btn').click(function () {
            EMandates.redirect();
        });
    }

    if (jQuery('#np-sign-aisp-btn').length || jQuery('#np-sign-aisp-redirect-btn').length) {
        jQuery('#np-sign-aisp-btn').click(function () {
            NuapayOpenBanking.showEmandateAisp(NP_Script_Params.authToken, NP_Script_Params.webUrl);
        });

        jQuery('#np-sign-aisp-redirect-btn').click(function () {
            NuapayOpenBanking.redirectEmandateAisp(NP_Script_Params.authToken, NP_Script_Params.webUrl);
        });
    }

    if (jQuery('#np-sign-pisp-btn').length || jQuery('#np-sign-pisp-redirect-btn').length) {
        switch (NP_Script_Params.currency) {
            case 'GBP':

                jQuery('#np-sign-pisp-btn').click(function () {
                    NuapayOpenBanking.showPaymentUI(NP_Script_Params.csid, NP_Script_Params.paymentUiid, NP_Script_Params.webUrl);
                });

                jQuery('#np-sign-pisp-redirect-btn').click(function () {
                    NuapayOpenBanking.redirectPaymentUI(NP_Script_Params.csid, NP_Script_Params.paymentUiid, NP_Script_Params.webUrl);
                });

                break;
            case 'EUR':

                jQuery('#np-sign-pisp-btn').click(function () {
                    NuapayOpenBanking.showEmandatePisp(NP_Script_Params.csid, NP_Script_Params.paymentUiid, NP_Script_Params.webUrl);
                });

                jQuery('#np-sign-pisp-redirect-btn').click(function () {
                    NuapayOpenBanking.redirectEmandatePisp(NP_Script_Params.csid, NP_Script_Params.paymentUiid, NP_Script_Params.webUrl);
                });

                break;
        }
    }
});