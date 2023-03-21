function toggleFlash(hide){if(navigator.platform.indexOf("Mac")!=-1){if(hide){try{SIFRReplace();}catch(ex){}}
var objects=document.getElementsByTagName('object'),i,il;for(i=0,il=objects.length;i<il;i++){objects[i].style.display=hide?'none':'block';objects[i].style.visibility=hide?'hidden':'visible';}
var embeds=document.getElementsByTagName('embed');for(i=0,il=embeds.length;i<il;i++){embeds[i].style.display=hide?'none':'block';embeds[i].style.visibility=hide?'hidden':'visible';}}}
if(typeof dhtmlwindow=="undefined")
alert('ERROR: Modal Window script requires all files from "DHTML Window widget" in order to work!')
var dhtmlmodal={veilstack:0,open:function(t,contenttype,contentsource,title,attr,recalonload){toggleFlash(true);var d=dhtmlwindow
this.interVeil=document.getElementById("interVeil")
this.veilstack++
this.loadveil()
if(typeof recalonload!="undefined"&&recalonload=="recal"&&d.scroll_top==0)
d.addEvent(window,function(){dhtmlmodal.loadveil()},"load")
var t=d.open(t,contenttype,contentsource,title,attr,recalonload)
t.show=function(){dhtmlmodal.show(this)}
t.hide=function(){dhtmlmodal.close(this)}
return t},loadveil:function(){var d=dhtmlwindow
d.getviewpoint()
this.docheightcomplete=(d.standardbody.offsetHeight>d.standardbody.scrollHeight)?d.standardbody.offsetHeight:d.standardbody.scrollHeight
this.interVeil.style.width=d.docwidth+"px"
this.interVeil.style.height=this.docheightcomplete+"px"
this.interVeil.style.left=0
this.interVeil.style.top=0
this.interVeil.style.visibility="visible"
this.interVeil.style.display="block"},adjustveil:function(){if(this.interVeil&&this.interVeil.style.display=="block")
this.loadveil()},close:function(t){t.contentDoc=(t.contentarea.datatype=="iframe")?window.frames["_iframe-"+t.id].document:t.contentarea
var closewinbol=dhtmlwindow.close(t)
if(closewinbol){this.veilstack--
if(this.veilstack==0)
this.interVeil.style.display="none"}},forceclose:function(t){toggleFlash(false);if(String(t)!="undefined"){dhtmlwindow.rememberattrs(t)
t.style.display="none";}
this.veilstack--
if(this.veilstack==0)
this.interVeil.style.display="none"},show:function(t){dhtmlmodal.veilstack++
dhtmlmodal.loadveil()
dhtmlwindow.show(t)}}
document.write('<div id="interVeil"></div>')
dhtmlwindow.addEvent(window,function(){if(typeof dhtmlmodal!="undefined")dhtmlmodal.adjustveil()},"resize")