/**
 * Created by Varenko Oleg on 05.06.2016.
 */
yii.pager = (function($) {

    var pub = function() {

            var jqBtnNextPage = $('.btn-show-next-page'),
                jqSelLinks = 'ul.pagination',
                jqSelNext = 'li.next',
                jqIndicator = jqBtnNextPage.children('img');

            var _self = this,
                isInitData = false,
                pathStatic = '/i/pager-stat-btn.gif',
                pathLoad = '/i/pager-loader-btn.gif',
                isRequest = false,
                successCallback = [],
                data = {},
                jqContainerContent = '',
                indicator = function(isLoad) {
                    var path = isLoad ? pathLoad : pathStatic;
                    jqIndicator.attr('src', path);
                },
                isExistsNextPage = function() {
                    var next = jqBtnNextPage.next(jqSelLinks).find(jqSelNext);
                    return !(next.hasClass('disabled'));
                },
                setCurrentLinks = function(html) {
                    jqBtnNextPage.next(jqSelLinks).remove();
                    jqBtnNextPage.parent().append(html);
                },
                //--- добавляем в урл номер страницы без перезагрузки
                setCurrentUrl = function(page) {

                    var params = yii.getQueryParams(window.location.search),
                        paramsNew = [];
                    $.each(params, function(i, e){
                        if ($.inArray(i, ['page', 'per-page']) == -1) {
                            paramsNew.push(i + '=' + e);
                        }
                    });
                    paramsNew.push('page=' + page);
                    window.history.pushState(null, null, window.location.pathname + '?' + paramsNew.join('&'));
                };

            this.init = function() {

                //--- тест
                jqBtnNextPage.click(function() {

                    if(!isRequest) {
                        isRequest = true;
                        indicator(true);

                        ajaxSendRequest({
                            action: 'getNextPage',
                            data: {
                                called: data.called,
                                params: data.params,
                                maxButtonCount: data.maxButtonCount,
                                defaultPageSize: data.defaultPageSize,
                                currentPage: data.currentPage,
                                pageSize: data.pageSize,
                                totalCount: data.totalCount
                            },
                            complete: function(response) {

                                jqContainerContent.append(response.data.html);
                                var totalCallback = successCallback.length;
                                if(totalCallback) {
                                    for(var i = 0; i < totalCallback; i++) {
                                        successCallback[i](response.data.params);
                                    }
                                }

                                if(response.data.pagerHtml.length) {
                                    setCurrentLinks(response.data.pagerHtml);
                                }

                                setCurrentUrl(data.currentPage);
                                if(!isExistsNextPage()) {
                                    jqBtnNextPage.remove();
                                }

                                indicator(false);
                                data.currentPage++;
                                isRequest = false;
                            },
                            error: function(response) {}
                        });
                    }
                });
            };

            this.initData = function() {
                if(!isInitData) {

                    data = window.widgets.Pager;
                    jqContainerContent = $('#' + data.container);
                    isInitData = true;
                }
            };

            // добавляем колбек
            this.setSuccessCallback = function(callback) {
                if($.isFunction(callback) && $.inArray(callback, successCallback) == -1) {
                    successCallback.push(callback);
                }
            }
        },
        Pub = new pub();
    $(document).ready(function() {
        Pub.initData();
    });
    return {
        self: Pub,
        init: function() {
            Pub.init();
            // генерируем событие инициализации виджета пейджера
            $(document).trigger('init-widget-pager', [Pub]);
        }
    };
})($);