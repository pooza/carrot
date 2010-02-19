function ShowTip(fArg)
{
	var tooltipOBJ = (document.getElementById) ? document.getElementById('tt' + fArg) : eval("document.all['tt" + fArg + "']");
	if (tooltipOBJ != null) {
		var tooltipLft = (document.documentElement.offsetWidth?document.documentElement.offsetWidth:document.body.style.pixelWidth) - (tooltipOBJ.offsetWidth?tooltipOBJ.offsetWidth:(tooltipOBJ.style.pixelWidth?tooltipOBJ.style.pixelWidth:380)) - 30;
		var tooltipTop = 10;
		if (navigator.appName == 'Netscape') {
			tooltipTop = (document.documentElement.scrollTop>=0?document.documentElement.scrollTop+10:event.clientY+10);
 			tooltipOBJ.style.top = tooltipTop+"px";
			tooltipOBJ.style.left = tooltipLft+"px";
		}
		else {
			tooltipTop = (document.documentElement.scrollTop>=0?document.documentElement.scrollTop+10:event.clientY+10);
			tooltipTop = (document.body.scrollTop>=0?document.body.scrollTop+10:event.clientY+10);
			tooltipOBJ.style.left = tooltipLft;
			tooltipOBJ.style.top = tooltipTop;
		}
		tooltipOBJ.style.visibility = "visible";
	}
}
function HideTip(fArg)
{
	var tooltipOBJ = (document.getElementById) ? document.getElementById('tt' + fArg) : eval("document.all['tt" + fArg + "']");
	if (tooltipOBJ != null) {
		tooltipOBJ.style.visibility = "hidden";
	}
}
