<script src="{{ asset('js/app.js') }}"></script>
@stack('vendor-js')
@auth<script src="{{ asset('js/custom.js') }}?v={{ config('app.version') }}"></script><script>@if ($message = session()->get('success'))alertApp('success','{{ $message }}'); @elseif ($message = session()->get('error'))alertApp('error','{{ $message }}'); @elseif ($message = session()->get('info'))alertApp('default','{{ $message }}'); @endif
$(document).on("click","#settings-side-bar",function(){$.ajax({url:"{{ route('settingsSideBar') }}",type:"POST",dataType:"JSON",success:function(t){t.status||alert(t.pesan)},error:function(t,s,e){alert(e)}})});</script>@endauth
@stack('js')
