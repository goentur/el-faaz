@forelse ($datas as $item)
<div class="col product">
     <div class="card shadow-sm m-0 border"> <img class="card-img-top" src="{{$item->foto!=null?$item->foto:asset('img/product/emty.png')}}" alt="" />
          <div class="m-2"> <a class="text-decoration-none text-dark stretched-link fw-12" title="{{$item->nama}}" href="javascript:void(0)">
                    <div class="mb-2">{{strlen($item->nama)>=60 ? substr($item->nama,0,60).'...':$item->nama}}</div>
               </a>
               <div class="row fw-12"> <span class="col-5 text-primary fw-bold">{{rupiah($item->harga_jual)}}</span> <span class="col-7 text-end"> <span class="badge bg-primary">M</span> <span class="badge bg-primary">L</span> <span class="badge bg-primary">XL</span> <span class="badge bg-primary">XXL</span> </span> </div>
          </div>
     </div>
</div>
@empty<span class="mt-5">DATA TIDAK DITEMUKAN</span>
@endforelse