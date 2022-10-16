<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Print Harga Produk</title>
</head>
<link rel="stylesheet" href="{{ asset('assets/css/output.css') }}">
<link rel="stylesheet" href="{{ asset('assets/fonts/pricetag.css') }}">
<style>
  @media print {
    @page {
      size: 210mm 330mm landscape
    }
  }
</style>
<body>
  <div class="p-8">
    <table class="w-full">
      <tbody>
        <tr class="flex">
          @foreach ($products as $i => $product)
            <td class="flex flex-col justify-start w-5cm my-1">
              <div class="wrapper border border-black bg-white ml-2 mb-2">
                <div class="max-w-[188px] relative mb-[36px] border-0 p-0">
                  <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-white text-center p-2 border-b-black border-b product @if(strlen($product->name) > 16) text-xs @else text-sm @endif ">{{ $product->name }}</h3>
                </div>
                <div class="flex items-center justify-between mb-1 border-b border-b-black">
                  <h3 class="text-xs rounded-sm px-2 font-bold">{{ $product->group->code }}</h3>
                  <img src="{{ asset('assets/images/logo-swakop.png') }}" class="w-10 h-4 flex justify-center" alt="Logo Swakop">
                  <h3 class="text-xs rounded-sm px-2 barcode">{{ $product->barcode }}</h3>
                </div>
                <div class="flex items-center justify-between px-3 price">
                  <p class="text-2xl font-semibold">Rp</p>
                  <h1 class="text-2xl font-bold">{{ $product->price?->price_per_unit ? number_format($product->price->price_per_unit, 0, '.', '') : 0 }}</h1>
                </div>
                @foreach ($product->price?->variableCosts as $j => $item)
                  <div class="flex items-center justify-center text-red-600 border-t border-t-black" style="border-color: black !important">
                    <p class="text-md price font-semibold">{{ $item?->qty }} pcs &nbsp; - &nbsp; </p>
                    <h1 class="text-md price font-bold">{{ $item?->price ? number_format($item->price, 0, '.', '') : 0 }}</h1>
                  </div>
                @endforeach
              </div>
            </td>
            @if($loop->index % 5 === 4 && $loop->index > 0)
            </tr>
              @if(($loop->index % 30 === 29 && $loop->index > 0) || $loop->index === 19)
              <tr class="flex" style="page-break-after: always">
              @else 
              <tr class="flex">
              @endif
            @endif
          @endforeach
        </tr>
      </tbody>
    </table>
  </div>
  <script>
    const nprogress = document.getElementById('nprogress')
    if (nprogress) {
      nprogress.style.display = 'none'
    }

    window.print()
  </script>
</body>
</html>