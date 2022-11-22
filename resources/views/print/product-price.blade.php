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
      size: 210mm 297mm landscape
    }
  }

  .pricetag {
    height: 3.75cm !important;
  }
</style>
<body>
  <div class="p-0">
    <table class="w-full">
      <tbody>
        <tr class="flex">
          @foreach ($products as $i => $product)
            <td class="flex flex-col justify-start w-5cm pricetag p-0">
              <div class="wrapper border border-black bg-white pricetag">
                <div class="max-w-[188px] relative mb-[36px] border-0 p-0">
                  <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-white text-center px-2 py-3 border-b-black border-b product text-xs">{{ $product->name }}</h3>
                </div>
                <div class="flex items-center justify-between border-b border-b-black">
                  <h3 class="text-xs rounded-sm px-2 font-bold">{{ $product->group->code }}</h3>
                  <img src="{{ asset('assets/images/logo-swakop.png') }}" class="w-10 h-4 flex justify-center" alt="Logo Swakop">
                  <h3 class="text-xs rounded-sm px-2 barcode">{{ $product->barcode }}</h3>
                </div>
                @if ($product->price?->variableCosts->count() > 0)
                  <div class="flex items-center justify-center px-3 price">
                    <p class="text-lg font-semibold" style="line-height: 1.5rem">Rp &nbsp;</p>
                    <h1 class="text-lg font-bold" style="line-height: 1.5rem">{{ $product->price?->price_per_unit ? number_format($product->price->price_per_unit, 0, '', '.') : 0 }}</h1>
                  </div>
                  @foreach ($product->price?->variableCosts->sortBy('min_qty')->take(3) as $j => $item)
                    <div class="flex items-center justify-center text-red-600 border-t border-t-black" style="border-color: black !important">
                      <p class="text-sm price font-semibold">{{ $item?->qty }} pcs &nbsp; - &nbsp; </p>
                      <h1 class="text-sm price font-bold">{{ $item?->price ? number_format($item->price, 0, '', '.') : 0 }}</h1>
                    </div>
                  @endforeach
                @else
                  <div class="flex items-center justify-center px-3 price">
                    <p class="text-lg font-semibold" style="line-height: 1.5rem">Rp &nbsp;</p>
                    <h1 class="text-lg font-bold" style="line-height: 1.5rem">{{ $product->price?->price_per_unit ? number_format($product->price->price_per_unit, 0, '', '.') : 0 }}</h1>
                  </div>
                  @foreach ([0, 1, 2] as $j => $item)
                    <div class="flex items-center justify-center text-red-600 border-t border-t-black" style="border-color: black !important">
                      <p class="text-sm price font-semibold">&nbsp;</p>
                      <h1 class="text-sm price font-bold">&nbsp;</h1>
                    </div>
                  @endforeach
                @endif
              </div>
            </td>
            @if($loop->index % 5 === 4)
            </tr>
              @if(($loop->index % 25 === 24) || $loop->index === 24)
              <tr class="flex" style="page-break-before: always">
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