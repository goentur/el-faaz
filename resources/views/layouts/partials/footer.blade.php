<footer class="footer">
     <div class="container-fluid">
          <div class="row text-muted">
               <div class="col-10 text-start">
                    <p class="mb-0">
                         {{ date('Y') }} &copy; <a href="{{ config('app.link_copyright') }}" target="_blank" class="text-muted"><strong>{{ config('app.copyright') }}</strong></a>
                    </p>
               </div>
               <div class="col-2 text-end">
                    <ul class="list-inline">
                         <li class="list-inline-item">
                              <span class="text-muted">{{ config('app.version') }}</span>
                         </li>
                    </ul>
               </div>
          </div>
     </div>
</footer>