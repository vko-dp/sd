/**
 * Created by Varenko Oleg on 03.07.2016.
 */
yii.cart = (function($) {

    var pub = function() {

            var _self = this;

            this.init = function() {

            };

            this.initData = function() {};
        },
        Pub = new pub();
    $(document).ready(function() {
        Pub.initData();
    });
    return {
        self: Pub,
        init: function() {
            Pub.init();
        }
    };
})($);