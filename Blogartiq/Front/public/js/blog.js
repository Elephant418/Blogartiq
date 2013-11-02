$(function(){
    (function articleExternalLinks() {
        $.expr[':'].external = function(obj){
            return !obj.href.match(/^mailto\:/)
              && (obj.hostname != location.hostname);
        };
        $('a:external').attr('target', '_blank');
    })();
});