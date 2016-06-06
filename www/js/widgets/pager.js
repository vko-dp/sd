/**
 * Created by Varenko Oleg on 05.06.2016.
 */
$(function() {

    //--- тест
    $('.btn-show-next-page').click(function() {
        var data = window.widgets.Pager;
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
            complete: function(response) {},
            error: function(response) {}
        });
    });
});