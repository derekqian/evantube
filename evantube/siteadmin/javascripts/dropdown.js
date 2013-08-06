var DDSPEED = 10;
var DDTIMER = 15;

// main function to handle the mouse events //
function ddMenu(id,dir) {
  var head = document.getElementById(id + '-ddheader');
  var cont = document.getElementById(id + '-ddcontent');
  clearInterval(cont.timer);
  if(dir == 1) {
    clearTimeout(head.timer);
    if(cont.maxh && cont.maxh <= cont.offsetHeight) {
      return;
    } else if(!cont.maxh) {
      cont.style.display = 'block';
      cont.style.height = 'auto';
      cont.maxh = cont.offsetHeight;
      cont.style.height = '0px';
    }
    cont.timer = setInterval("ddSlide('" + id + "-ddcontent', 1)", DDTIMER);
  } else {
    head.timer = setTimeout('ddCollapse(\'' + id + '-ddcontent\')', 50);
  }
}

// collapse the menu //
function ddCollapse(id) {
  var cont = document.getElementById(id);
  cont.timer = setInterval("ddSlide('" + id + "', -1)", DDTIMER);
}

// cancel the collapse if a user rolls over the dropdown content //
function cancelHide(id) {
  var head = document.getElementById(id + '-ddheader');
  var cont = document.getElementById(id + '-ddcontent');
  clearTimeout(head.timer);
  clearInterval(cont.timer);
  if(cont.offsetHeight < cont.maxh) {
    cont.timer = setInterval("ddSlide('" + id + "-ddcontent', 1)", DDTIMER);
  }
}

// incrementally expand/contract the dropdown and change the opacity //
function ddSlide(id,dir) {
  var cont = document.getElementById(id);
  var currheight = cont.offsetHeight;
  var dist;
  if(dir == 1) {
    dist = (Math.round((cont.maxh - currheight) / DDSPEED));
  } else {
    dist = (Math.round(currheight / DDSPEED));
  }
  if(dist <= 1 && dir == 1) {
    dist = 1;
  }
  cont.style.height = currheight + (dist * dir) + 'px';
  cont.style.opacity = currheight / cont.maxh;
  cont.style.filter = 'alpha(opacity=' + (currheight * 100 / cont.maxh) + ')';
  if((currheight < 2 && dir != 1) || (currheight > (cont.maxh - 2) && dir == 1)) {
    clearInterval(cont.timer);
  }
}