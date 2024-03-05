$(function() {
     $.ajaxSetup({
          headers: {
               "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          }
     })
})
function alertApp(t,m,b = false) {
     window.notyf.open({
          type: t,
          message: m,
          position: {
               x: 'left',
               y: 'top',
          },
     })
     if (b) {
          $.unblockUI()
     }
}
function currency(r, t, i, n) {
     if (null == r || !isFinite(r)) throw TypeError("number is not valid");
     if (!t) {
          var e = r.toString().split(".").length;
          t = e > 1 ? e : 0
     }
     i || (i = "."), n || (n = ",");
     var l = (r = (r = parseFloat(r).toFixed(t)).replace(",", i)).split(i);
     return l[0] = l[0].replace(/\B(?=(\d{3})+(?!\d))/g, n), r = l.join(i)
}
function loading() {
     $.blockUI({
          css: {
               backgroundColor: "transparent",
               border: "none"
          },
          message: '<div class="text-primary spinner-border" style="width: 3rem; height: 3rem;" role="status"><span class="sr-only">Loading</span> </div><h4 class="text-primary">LOADING</h4>',
          baseZ: 1500,
          overlayCSS: {
               backgroundColor: "#FFFFFF",
               opacity: .4,
               cursor: "wait"
          }
     })
}
