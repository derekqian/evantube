
//--------------------------------------------------------------------------------------------//
function disable_link(id)
{
	var ajax_link = document.getElementById(id);
	ajax_link.style.display = 'none';
}

//--------------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------------//

function showBlock(id)
{
	var myWindow = document.getElementById(id);
	myWindow.style.visibility = "visible";
}

function closeBlock(id)
{
	var myWindow = document.getElementById(id);
	myWindow.style.visibility = "hidden";
}

function switchMenu(obj)
{
	var el = document.getElementById(obj);
	if ( el.style.display != "none" ) {
		el.style.display = 'none';

		if ( el == 'blog-form' ) {
			el.style.height = '0px';
		}

	}
	else {
		el.style.display = '';
	}
}

//--------------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------------//

var imgExpand = new Image();
imgExpand.src = 'themes/default/images/icons/close.gif';

var imgCollapse = new Image();
imgCollapse.src = 'themes/default/images/icons/open.gif';

function toggle(img)
{
	var e = document.getElementById(img);
	var i = document.images[img];
	i.src = (i.src==imgExpand.src) ? imgCollapse.src : imgExpand.src;
}

//--------------------------------------------------------------------------------------------//

//--------------------------------------------------------------------------------------------//

if (document.images) {
	imgUpOn = new Image
	imgUpOn.src = 'themes/default/images/thumbs_up_over.png';
	imgUpOff = new Image
	imgUpOff.src = 'themes/default/images/thumbs_up.png';
	imgDownOn = new Image
	imgDownOn.src = 'themes/default/images/thumbs_down_over.png';
	imgDownOff = new Image
	imgDownOff.src = 'themes/default/images/thumbs_down.png';
}

function over_image_up(img, id)
{
	var e = document.getElementById(img);
	var i = document.images[img];
	i.src = (i.src==imgUpOn.src) ? imgUpOff.src : imgUpOn.src;
}

function off_image_up(img, id)
{
	var e = document.getElementById(img);
	var i = document.images[img];
	i.src = (i.src==imgUpOn.src) ? imgUpOff.src : imgUpOn.src;
}

function over_image_down(img, id)
{
     	var ee = document.getElementById(img);
	var ii = document.images[img];
	ii.src = (ii.src==imgDownOn.src) ? imgDownOff.src : imgDownOn.src;
}

function off_image_down(img, id)
{
     	var ee = document.getElementById(img);
	var ii = document.images[img];
	ii.src = (ii.src==imgDownOn.src) ? imgDownOff.src : imgDownOn.src;
}

///////////////////////////////////////////////////////////////////////////////////////////////

function ajax_refresh(msg, id, value, rate)
{
     	var rate_div = document.getElementById(id);
     	var rating = value+rate;
	rate_div.innerHTML = '<div style="margin-left:0px; float:left;" id="rating_vote_38">Rated:&nbsp;'+rating+'</div>';
}

function end_vote(div_id, div_id_new)
{
	var has_voted = document.getElementById(div_id);
	var voted_img = document.getElementById(div_id_new);
	if ( has_voted.style.display != "none" ) {
		has_voted.style.display = 'none';
		voted_img.style.display = 'block';
	}
	else {
		has_voted.style.display = '';
	}
}
