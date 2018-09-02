/* Functions */
var modallen = {
	alert: function(header,msg)
	{
		$("#alert-header").html(header);
		$("#alert-body").html(msg);
		M.Modal.getInstance($("#alert")).open();
	}
}
/* /Functions */


$(document).ready(function(){
	$(".dropdown-trigger").dropdown();
	$('.modal').modal({startingTop: '30%', endingTop: '50%'});
	
	/* Event Listeners */
	$('.preventDefault').click(function(e){ e. preventDefault(); })
	$('.ical').click(function(e){
		e.preventDefault();
		e.stopPropagation();
		
		pretext = this.dataset.pretext;
		pretext += "<p>Copy this URL into your favourite calendar app and start syncing:</p>"; 
		modallen.alert($(this).html() + " Calendar", pretext + "<div class='cal_link code center'>" + this.href + "</div>");
	})
	/* /Event Listeners */
})