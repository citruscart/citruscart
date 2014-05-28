/**

*/
function socialsharing_twitter_click(message)
{
	if (typeof message === 'undefined')
		message = encodeURIComponent(location.href);
	window.open('https://twitter.com/intent/tweet?text=' + message, 'sharertwt', 'toolbar=0,status=0,width=640,height=445');
}

function socialsharing_facebook_click(message)
{
	window.open('http://www.facebook.com/sharer.php?u=' + encodeURIComponent(location.href), 'sharer', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_google_click(message)
{
	window.open('https://plus.google.com/share?url=' + encodeURIComponent(location.href), 'sharergplus', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_pinterest_click(message)
{
	window.open('http://www.pinterest.com/pin/create/button/?url=' + encodeURIComponent(location.href), 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}

function socialsharing_linkedin_click(message)
{
	window.open('http://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(location.href), 'sharerpinterest', 'toolbar=0,status=0,width=660,height=445');
}