<table class="table">
@foreach($coas as $item)
<tr><td>{{ $item->kode_akun }}</td><td>{{ $item->nama_akun }}</td><td>{{ $item->label_header_akun }}</td></tr>
@endforeach
</table>
