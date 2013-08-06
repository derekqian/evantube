// Show/Hide Media Description
function show(){
        document.getElementById('shortdesc').style.visibility="hidden";
        document.getElementById('shortdesc').style.display="none";
        document.getElementById('longdesc').style.display="block";
        document.getElementById('longdesc').style.visibility="visible";
        document.getElementById('showlink').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hide()\">Show Less <img src=\"addons/mw_eclipse/skin/images/bullet_7.png\" alt=\"\" /><\/a>";

}
function hide(){
        document.getElementById('shortdesc').style.visibility="visible";
        document.getElementById('shortdesc').style.display="block";
        document.getElementById('longdesc').style.display="none";
        document.getElementById('longdesc').style.visibility="hidden";
        document.getElementById('showlink').innerHTML="<a style=\"cursor:pointer;\" onClick=\"show()\">Show More <img src=\"addons/mw_eclipse/skin/images/bullet_6.png\" alt=\"\" /><\/a>";
}

// Show/Hide Embed Code and Link
function showembed(){
        document.getElementById('hideembed').style.visibility="hidden";
        document.getElementById('hideembed').style.display="none";
        document.getElementById('showembed').style.display="block";
        document.getElementById('showembed').style.visibility="visible";
        document.getElementById('embed').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hideembed()\"><img src=\"addons/mw_eclipse/skin/images/icons/icon_share.png\" align=\"absmiddle\" />&nbsp;Share / Embed<\/a>";

}
function hideembed(){
        document.getElementById('hideembed').style.visibility="visible";
        document.getElementById('hideembed').style.display="block";
        document.getElementById('showembed').style.display="none";
        document.getElementById('showembed').style.visibility="hidden";
        document.getElementById('embed').innerHTML="<a style=\"cursor:pointer;\" onClick=\"showembed()\"><img src=\"addons/mw_eclipse/skin/images/icons/icon_share.png\" align=\"absmiddle\" />&nbsp;Share / Embed<\/a> ";
}

// Show/Hide More Info Member Profile
function moreinfo(){
        document.getElementById('lessinfo').style.visibility="hidden";
        document.getElementById('lessinfo').style.display="none";
        document.getElementById('moreinfo').style.display="block";
        document.getElementById('moreinfo').style.visibility="visible";
        document.getElementById('embed').innerHTML="<a style=\"cursor:pointer;\" onClick=\"lessinfo()\">Less Info<\/a>";

}
function lessinfo(){
        document.getElementById('lessinfo').style.visibility="visible";
        document.getElementById('lessinfo').style.display="block";
        document.getElementById('moreinfo').style.display="none";
        document.getElementById('moreinfo').style.visibility="hidden";
        document.getElementById('embed').innerHTML="<a style=\"cursor:pointer;\" onClick=\"moreinfo()\">More Info<\/a> ";
}


// Show/Hide Password Reminder
function showforgot(){
        document.getElementById('hideforgot').style.visibility="hidden";
        document.getElementById('hideforgot').style.display="none";
        document.getElementById('showforgot').style.display="block";
        document.getElementById('showforgot').style.visibility="visible";
        document.getElementById('forgot').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hideforgot()\">Hide Password Reminder<\/a>";

}
function hideforgot(){
        document.getElementById('hideforgot').style.visibility="visible";
        document.getElementById('hideforgot').style.display="block";
        document.getElementById('showforgot').style.display="none";
        document.getElementById('showforgot').style.visibility="hidden";
        document.getElementById('forgot').innerHTML="Forgot password? &nbsp; <a style=\"cursor:pointer;\" onClick=\"showforgot()\">Email new password<\/a>";
}


// Show/Hide Additional Upload Options
function showuploadoptions(){
        document.getElementById('hideuploadoptions').style.visibility="hidden";
        document.getElementById('hideuploadoptions').style.display="none";
        document.getElementById('showuploadoptions').style.display="block";
        document.getElementById('showuploadoptions').style.visibility="visible";
        document.getElementById('options').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hideuploadoptions()\">Hide Optional Info<\/a>";

}
function hideuploadoptions(){
        document.getElementById('hideuploadoptions').style.visibility="visible";
        document.getElementById('hideuploadoptions').style.display="block";
        document.getElementById('showuploadoptions').style.display="none";
        document.getElementById('showuploadoptions').style.visibility="hidden";
        document.getElementById('options').innerHTML="<a style=\"cursor:pointer;\" onClick=\"showuploadoptions()\">Show Optional Info<\/a>, or";
}


// Show/Hide Create New Photo Album
function shownewalbum(){
        document.getElementById('hidenewalbum').style.visibility="hidden";
        document.getElementById('hidenewalbum').style.display="none";
        document.getElementById('shownewalbum').style.display="block";
        document.getElementById('shownewalbum').style.visibility="visible";
        document.getElementById('photoalbum').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hidenewalbum()\">Hide Create New Album<\/a>";

}
function hidenewalbum(){
        document.getElementById('hidenewalbum').style.visibility="visible";
        document.getElementById('hidenewalbum').style.display="block";
        document.getElementById('shownewalbum').style.display="none";
        document.getElementById('shownewalbum').style.visibility="hidden";
        document.getElementById('photoalbum').innerHTML="<a style=\"cursor:pointer;\" onClick=\"shownewalbum()\">Create New Album<\/a>";
}


// Show/Hide Change Audio Album Cover
function showalbumcover(){
        document.getElementById('hidealbumcover').style.visibility="hidden";
        document.getElementById('hidealbumcover').style.display="none";
        document.getElementById('showalbumcover').style.display="block";
        document.getElementById('showalbumcover').style.visibility="visible";
        document.getElementById('options').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hidealbumcover()\">Hide Change Album Cover<\/a>";

}
function hidealbumcover(){
        document.getElementById('hidealbumcover').style.visibility="visible";
        document.getElementById('hidealbumcover').style.display="block";
        document.getElementById('showalbumcover').style.display="none";
        document.getElementById('showalbumcover').style.visibility="hidden";
        document.getElementById('options').innerHTML="<a style=\"cursor:pointer;\" onClick=\"showalbumcover()\">Upload album cover, or change existing album cover<\/a>";
}


// Show/Hide User Panel
function user_panel(){
        document.getElementById('hide_user_panel').style.visibility="hidden";
        document.getElementById('hide_user_panel').style.display="none";
        document.getElementById('user_panel').style.display="block";
        document.getElementById('user_panel').style.visibility="visible";
        document.getElementById('userpanel').innerHTML="<a style=\"cursor:pointer;\" onClick=\"hide_user_panel()\">hide this <img src=\"addons/mw_eclipse/skin/images/bullet_7.png\" alt=\"\" /><\/a>";

}
function hide_user_panel(){
        document.getElementById('hide_user_panel').style.visibility="visible";
        document.getElementById('hide_user_panel').style.display="block";
        document.getElementById('user_panel').style.display="none";
        document.getElementById('user_panel').style.visibility="hidden";
        document.getElementById('userpanel').innerHTML="<a style=\"cursor:pointer;\" onClick=\"user_panel()\">more by this member <img src=\"addons/mw_eclipse/skin/images/bullet_6.png\" alt=\"\" /><\/a>";
}
