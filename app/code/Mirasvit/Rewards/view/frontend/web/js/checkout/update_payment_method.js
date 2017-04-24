define(
    [
        'jquery',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/totals',
        'Mirasvit_Rewards/js/view/checkout/rewards/points_totals'
    ],
    function(
        $,
        quote,
        totals,
        rewardsEarn
    ) {
        'use strict';
        return function (data) {
            if (typeof data == 'undefined') {
                data = {payment: quote.paymentMethod()};
            } else if (typeof data['payment'] == 'undefined') {
                data.payment = quote.paymentMethod();
            }

            totals.isLoading(true);
            $.ajax({
                url: window.checkoutConfig.chechoutRewardsPaymentMethodPointsUrl,
                type: 'POST',
                dataType: 'JSON',
                data: data,
                complete: function (data) {
                    rewardsEarn().isDisplayed(data.responseJSON.success);
                    rewardsEarn().getValue(data.responseJSON.points);
                    totals.isLoading(false);
                },
                error: function (data) {
                    totals.isLoading(false);
                }
            });
        }
    }
);