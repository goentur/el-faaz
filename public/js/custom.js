$(function() {
     $.ajaxSetup({
          headers: {
               "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
          }
     })
})
function alertApp(t,m) {
     window.notyf.open({
          type: t,
          message: m,
          position: {
               x: 'left',
               y: 'top',
          },
     })
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
