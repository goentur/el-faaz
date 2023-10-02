$(function() {
     $.ajaxSetup({
          headers: {
               "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          }
     })
})
function alertApp(t,m) {
     window.notyf.open({type: t,message: m})
}