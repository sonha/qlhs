document.write('<style>');
document.write('.awem3NBSPM { 	BORDER-RIGHT: #ffffff 1px solid;  BORDER-TOP: #ccee7c 1px solid;  FONT-WEIGHT: normal;  FONT-SIZE: 12px;  Z-INDEX: 1000;  VISIBILITY: hidden;  BORDER-LEFT: #ccee7c 1px solid;  WIDTH: 170px;  CURSOR: hand;  COLOR: #ffffff;  LINE-HEIGHT: 20px;  BORDER-BOTTOM: #ccee7c 1px solid;  FONT-STYLE: normal;  FONT-FAMILY: arial,helvetica,verdana;  POSITION: absolute;  BACKGROUND-COLOR: #ccee7c; }');
document.write('.awemnbspm { 	PADDING-LEFT: 21px;  	TEXT-ALIGN: left; } ');
document.write('.awemnbspm2 { 	PADDING-LEFT: 21px;  	TEXT-ALIGN: left; 	CURSOR: default;  }');
document.write('.awemnbspm3 { 	PADDING-LEFT: 9px;  	TEXT-ALIGN: left; 	CURSOR: default;  	FONT-STYLE: bold; 	BACKGROUND-COLOR: #84c02d;}');
document.write('DIV.c1  { 	Z-INDEX: 1;  	LEFT: -27px;  	WIDTH: 33px;  	POSITION: absolute;  	TOP: 107px;  	HEIGHT: 31px }');
document.write('</style>');

px =  "px";
timer1	= null;
var decrease	= 0.1; 
var offset = 0;
var selected_id = null;

function getPosition(id) 
{ 
	endPos = id.offsetTop;
}	

function actionMenu(obj,steps) 
{
	if (document.getElementById) 
	{
		el = document.getElementById(obj) ;
	}
		el.xpos = el.offsetTop;
	if (el.xpos < endPos) 
	{
		clearTimeout(timer1);
	}
	else if (el.xpos >= endPos) 
	{
		clearTimeout(timer1);
	}
		distance = endPos - el.xpos + offset;
		steps = distance*decrease; 
		el.xpos += steps;
		el.style.top = el.xpos+px ;
		timer1= setTimeout("actionMenu('" + obj + "')",25);
}


document.write('<div class="awem3NBSPM" id="awem2NBSPM" onmouseover="Awemnbspm(event)" style="visibility: hidden; left: 437px; top: 150px;" onclick="awEmnbspm(event)" onmouseout="aWemnbspm(event)" display:none="">');

document.write('<DIV class=c1 id=pointer><IMG src="../images/parallels.png" border=0> </DIV>');
document.write('<div onmouseover="getPosition(this);actionMenu(\'pointer\',\'20\')" style="background-color: rgb(204, 238, 124); color: rgb(255, 255, 255);" class="awemnbspm" description="CTN Việt Nam" target="_self" url="http://ctnvietnam.com">http://ctnvietnam.com</div>');
document.write('<div onmouseover="getPosition(this);actionMenu(\'pointer\',\'20\')" style="background-color: rgb(204, 238, 124); color: rgb(255, 255, 255);" class="awemnbspm" description="CTN Việt Nam" target="_self" url="http://ctnvietnam.vn">http://ctnvietnam.vn</div>');
document.write('<div class="awemnbspm2"><hr /></div>');
document.write('<div style="background-color: rgb(132, 192, 45); color: rgb(255, 255, 255);" class="awemnbspm2" description="Design by IT CTN Việt Nam" target="_self" url="http://ctnvietnam.com">Design by CTN IT</div></div>');

var aweMnbspm=1;var AWemnbspm="#84c02d";var aWEmnbspm="#000000";var awEMnbspm="#ccee7c";var AWEmnbspm="#FFFFFF";var aWEMnbspm=document.getElementById&&document.all;var AWEMnbspm=document.getElementById&&!document.all;if(aWEMnbspm||AWEMnbspm) {var awemNbspm=document.getElementById("awem2NBSPM");}
function AwemNbspm(e){
var tn=aWEMnbspm?event.srcElement:e.target;
if (tn + '' != '[object]')
{
	var aweMNbspm=awemNbspm.getElementsByTagName("div");
	if (tn.toString().indexOf('http:')==0 || tn.toString().indexOf('ftp:')==0 || tn.toString().indexOf('https:')==0 || tn.toString().indexOf('file:')==0)
	{
		//awemNbspm.style.visibility="hidden";
		//return;
		for ( i=0; i<aweMNbspm.length; i++)
		{
			try
			{
				if(aweMNbspm[i].getAttribute("op")=='1')
				{
					aweMNbspm[i].setAttribute("url",tn.toString());
					aweMNbspm[i].style.display='';
				}
			}
			catch (e){}
		}
	}
	else
	{
		for ( i=0; i<aweMNbspm.length; i++)
		{
			try
			{
				if(aweMNbspm[i].getAttribute("op")=="1")
				{
					aweMNbspm[i].style.display="none";
				}
			}
			catch (e){}
		}
	}
}
else
{
	var aweMNbspm=awemNbspm.getElementsByTagName("div");
	for ( i=0; i<aweMNbspm.length; i++)
	{
		try
		{
			if(aweMNbspm[i].getAttribute("op")=="1")
			{
					aweMNbspm[i].style.display="none";
			}
		}
		catch (e){}
	}
}

//alert(document.body.scrollTop);
var aWemNbspm=aWEMnbspm?document.body.clientWidth-event.clientX:window.innerWidth-e.clientX;
var awEmNbspm=aWEMnbspm?document.body.clientHeight-event.clientY:window.innerHeight-e.clientY;

//endPos = id.offsetTop;

if (aWemNbspm<awemNbspm.offsetWidth)
{
	awemNbspm.style.left=aWEMnbspm? document.body.scrollLeft + event.clientX - awemNbspm.offsetWidth:window.pageXOffset+e.clientX-awemNbspm.offsetWidth;
}
else{
	awemNbspm.style.left=aWEMnbspm? document.body.scrollLeft+event.clientX:window.pageXOffset+e.clientX;
}
var aweMNbspm=awemNbspm.getElementsByTagName("div");

if (awEmNbspm<awemNbspm.offsetHeight && (aWEMnbspm?event.clientY:e.clientY )>=awemNbspm.offsetHeight)
{
	awemNbspm.style.top=aWEMnbspm? document.body.scrollTop+event.clientY-awemNbspm.offsetHeight:window.pageYOffset+e.clientY-awemNbspm.offsetHeight;
}
else
{
	awemNbspm.style.top=aWEMnbspm? document.body.scrollTop+event.clientY:window.pageYOffset+e.clientY;
	if (awEmNbspm<awemNbspm.offsetHeight)
	{
		awemNbspm.style.top = (aWEMnbspm? document.body.scrollTop+event.clientY:window.pageYOffset+e.clientY) - (awemNbspm.offsetHeight-awEmNbspm);
		if (awemNbspm.style.top < (aWEMnbspm?document.body.scrollTop:window.pageYOffset))
			awemNbspm.style.top = aWEMnbspm?document.body.scrollTop:window.pageYOffset;
	}	
}
awemNbspm.style.visibility="visible";return false;};
function AWemNbspm(e){awemNbspm.style.visibility="hidden";};
function Awemnbspm(e){var aWEmNbspm=aWEMnbspm? event.srcElement:e.target;if(aWEmNbspm.className=="awemnbspm"||AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){if(AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){aWEmNbspm=aWEmNbspm.parentNode;}var aweMNbspm=awemNbspm.getElementsByTagName("div");if(aWEMnbspm){
/* if(aweMNbspm[aweMNbspm.length-1].outerText.charAt(15)!=String.fromCharCode(84))return; */
}
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("url").charAt(20)!=String.fromCharCode(111))return;
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("description").charAt(22)!=String.fromCharCode(121))return;
aWEmNbspm.style.backgroundColor=AWemnbspm;aWEmNbspm.style.color=aWEmnbspm;if(aWEmNbspm.getAttribute("description")){window.status=aWEmNbspm.getAttribute("description");}else if (aWEmNbspm.getAttribute("url")){window.status=aWEmNbspm.getAttribute("url");}}};function aWemnbspm(e){var aWEmNbspm=aWEMnbspm?event.srcElement:e.target;if(aWEmNbspm.className=="awemnbspm"||AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){if(AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){aWEmNbspm=aWEmNbspm.parentNode;}var aweMNbspm=awemNbspm.getElementsByTagName("div");if(aWEMnbspm){
//if(aweMNbspm[aweMNbspm.length-1].outerText.charAt(15)!=String.fromCharCode(84))return;
}
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("url").charAt(20)!=String.fromCharCode(111))return;
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("description").charAt(22)!=String.fromCharCode(121))return;
aWEmNbspm.style.backgroundColor=awEMnbspm;aWEmNbspm.style.color=AWEmnbspm;window.status='';}};function awEmnbspm(e){var aWEmNbspm=aWEMnbspm?event.srcElement:e.target;if (aWEmNbspm.className=="awemnbspm"||AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){if (AWEMnbspm&&aWEmNbspm.parentNode.className=="awemnbspm"){aWEmNbspm=aWEmNbspm.parentNode;}var aweMNbspm=awemNbspm.getElementsByTagName("div");
if(aWEMnbspm){
//if(aweMNbspm[aweMNbspm.length-1].outerText.charAt(15)!=String.fromCharCode(84))return;
}
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("url").charAt(20)!=String.fromCharCode(111))return;
//if(aweMNbspm[aweMNbspm.length-1].getAttribute("description").charAt(22)!=String.fromCharCode(121))return;
if (aWEmNbspm.getAttribute("target")&&(aWEmNbspm.getAttribute("target")!="_self")){window.open(aWEmNbspm.getAttribute("url"),aWEmNbspm.getAttribute("target"));}else{window.location=aWEmNbspm.getAttribute("url");}}};if (aWEMnbspm||AWEMnbspm){awemNbspm.style.display='';
document.oncontextmenu=AwemNbspm;document.onclick=AWemNbspm;};
