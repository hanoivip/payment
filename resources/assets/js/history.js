/**
 * 
 */

$(document).ready(function(){
	$('.webtopup-history-page').on('click', function () {
		var page = $(this).attr('data-page')
		console.log("load history page " + page)
		var url = $(this).attr('data-action')
		var updateId = $(this).attr('data-update-id')
		$.ajax({
            url: url + "?page=" + page,
            method: 'GET',
            cache: false,
            processData: false,
            success:function(response)
            {
            	console.log(response)
            	$('#' + updateId).html(response)
            },
            error: function(response) {
            	console.log(response)
            }
        });
	})
});