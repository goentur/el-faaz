<div class="btn-group">
     <a href="{{ route($attribute['link'].'edit',enkrip($data->id)) }}" class="btn btn-sm btn-icon waves-effect waves-light btn-success"><i class="fa fa-pencil-alt"></i></a>
     <button class="btn btn-sm btn-icon waves-effect waves-light btn-danger hapus" data-id="{{ enkrip($data->id) }}"><i class="fas fa-trash-alt"></i></button>
</div>