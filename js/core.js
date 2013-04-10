// http://blog.fedecarg.com/2011/07/12/javascript-asynchronous-script-loading-and-lazy-loading/
var loader=function(a,b){b = b||function(){};for(var c=a.length,d=c,e=function(){
    if(!(this.readyState&&this.readyState!=="complete"&&this.readyState!=="loaded")){
    this.onload=this.onreadystatechange=null;--d||b()}},f=document.getElementsByTagName("head")[0],
    g=function(a){var b=document.createElement("script");b.async=true;
    b.src=a;b.onload=b.onreadystatechange=e;f.appendChild(b)};c;)g(a[--c])};

var populate_tree = null;
var populate_tree_ex = null;
var parent_path = null;

loader (['http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js'], function () {
    $(document).ready (function () {
        var rot13 = function (s) {
            return s.replace(/[a-zA-Z]/g,function(c){
            return String.fromCharCode((c<="Z"?90:122)>=(c=c.charCodeAt(0)+13)?c:c-26);})
        };
    });
});
