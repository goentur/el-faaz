@if ($data->foto!=null)<a href="{{ url($data->foto) }}" target="popup" onclick="window.open('{{ url($data->foto) }}','{{ $data->nama }}','width=800,height=600')"><img src="{{ url($data->foto) }}" alt="{{ $data->nama }}" class="img-fluid" width="85%"></a>@else<span class="badge bg-danger">FOTO KOSONG</span>@endif