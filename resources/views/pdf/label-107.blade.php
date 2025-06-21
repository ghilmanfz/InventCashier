@php
    // set ukuran label dalam mm (37×25) & margin kecil
    $col = 8; $row = 10;
    $w = 37; $h = 25;
@endphp
<!doctype html>
<html>
<head>
    <style>
        @page { margin: 0; }
        body  { margin: 4mm; font-size:9pt; font-family: Arial, sans-serif; }
        table { border-collapse: collapse; }
        td    { width:{{ $w }}mm;height:{{ $h }}mm; padding:0; text-align:center; }
        .name { font-weight:bold; font-size:7pt; line-height:1; }
        .sku  { font-size:6pt; line-height:1; }
        img   { width:100%; height:auto; }
    </style>
</head>
<body>
<table>
@foreach($chunks as $chunk)
    <tr>
    @foreach($chunk as $product)
        <td>
            <div class="name">{{ \Illuminate\Support\Str::limit($product->name,25) }}</div>
            <img src="{{ $product->barcode_svg }}" alt="">
            <div class="sku">{{ $product->sku }}</div>
        </td>
    @endforeach
    @for($i=count($chunk);$i<$col;$i++) <td></td> @endfor
    </tr>
@endforeach
</table>
</body>
</html>
