<?php

$html = <<<END_HTML
<!DOCTYPE html><html lang="en"><head>
<link href="https://fonts.googleapis.com/css?family=Share+Tech+Mono" rel="stylesheet">
<title>Priv8Chat :: 404 Not Found</title><style>*{margin:0;padding:0;}body{background:
#000;width:100vw;height:100vh;overflow:hidden;font-family:'Share Tech Mono',monospace;}
canvas{display:block;}div{position:fixed;top:50vh;width:100vw;text-align:center;font-size:
15vh;color:rgba(0,255,60,.7);transform:translateY(-50%);}a:link,a:visited{color:#6f8;
text-shadow:0 0 1.5vmin #6f8;text-decoration:none;}a:hover{color:#3f4;text-shadow:0 0
1.5vmin #3f4;}a:active{text-decoration:none;color:#9fc;text-shadow:0 0 1.5vmin #9fc;}
</style></head><body onload="makeitrain()" onresize="clearInterval(t);makeitrain()">
<canvas id="c"></canvas><div><a href="/">404 NOT FOUND</a></div><script>var h,w,d=[],s=[],
q=[],a="0123456789ｱｲｳｴｵｶｷｸｹｺｻｼｽｾｿﾀﾁﾂﾃﾄﾅﾆﾇﾈﾉﾊﾋﾌﾍﾎﾏﾐﾑﾒﾓﾔﾕﾖﾗﾘﾙﾚﾛﾜﾝ".split(""),c,x,m,i,t;function e()
{x.fillStyle="rgba(0,0,0,0.05)";x.fillRect(0,0,c.width,c.height);x.fillStyle="#384";x.font
=h+"px Arial";for(i=0;i<d.length;i++){q[i]+=s[i];if(q[i]>1){d[i]++;q[i]-=1;x.fillText(a[
Math.floor(Math.random()*a.length)],i*w,d[i]*h);if(d[i]*h>c.height){d[i]=0;s[i]=Math.random
()*.9+.1;}}}}function makeitrain(){c=document.getElementById("c");c.height=window.innerHeight;
c.width=window.innerWidth;h=Math.max(12,Math.min(c.width,c.height)/45);w=h*.7;m=c.width/w;x=
c.getContext("2d");t=setInterval(e,50);for(i=0;i<m;i++){d[i]=Math.random()*(c.height/h);s[i]=
Math.random()*.9+.1;q[i]=0;}}</script></body></html><!--Hat tip: codepen.io/P3R0/pen/MwgoKv-->
END_HTML;

header("HTTP/1.0 404 Not Found");
header("Content-Length: " . strlen($html));
header("Cache-Control: max-age=3600, public");
header("Content-Type: text/html; charset=utf8");

die($html);
