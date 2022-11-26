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

  .not-variable {
    background-color: #FFF14F !important;
  }

  .variable-price {
    font-size: 1rem;
    line-height: 1.15rem !important;
  }

  .bc-red {
    border-color: #EF4444 !important;
  }

  .bc-black {
    border-color: #000 !important;
  }
</style>
<body>
  <div class="p-0">
    <table class="w-full">
      <tbody>
        <tr class="flex">
          @foreach ($products as $i => $product)
            <td class="flex flex-col justify-start w-5cm pricetag p-0">
              <div class="wrapper border border-black bg-white kingthings">
                <div class="max-w-5cm relative mb-[36px] border-0 p-0">
                  <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-white text-center px-2 py-3 border-b-black border-b bc-black product text-sm kingthings">{{ $product->name }}</h3>
                </div>
                <div class="px-1">
                  <div class="flex items-center justify-center px-3 border-b border-b-red-500 bc-red">
                    <p class="text-xl font-bold" style="line-height: 1.5rem">Rp &nbsp;</p>
                    <h1 class="text-xl font-bold" style="line-height: 1.5rem">{{ $product->price?->price_per_unit ? number_format($product->price->price_per_unit, 0, '', '.') : 0 }}</h1>
                  </div>
                  <table class="w-full">
                    @if ($product->price?->variableCosts->count() > 0)
                      @foreach ($product->price?->variableCosts->sortBy('min_qty')->take(3) as $j => $item)
                        <tr class="text-black border-t border-t-red-500 bc-red">
                          <td class="variable-price px-2 text-left">{{ $item?->qty }} pcs</td>
                          <td class="variable-price px-2 text-center"> - </td>
                          <td class="variable-price px-2 text-right">Rp. {{ $item?->price ? number_format($item->price, 0, '', '.') : 0 }}</td>
                        </tr>
                      @endforeach
                      @if ($product->price?->variableCosts->count() < 3)
                        @for ($i = 0; $i < 3 - $product->price?->variableCosts->count(); $i++)
                          <tr class="text-black border-t border-t-red-500 bc-red">
                            <td class="variable-price text-white"> - </td>
                          </tr>
                        @endfor
                      @endif
                    @else
                      @foreach ([0, 1, 2] as $j => $item)
                        <tr class="text-black border-t border-t-red-500 bc-red">
                          <td class="variable-price text-white"> - </td>
                        </tr>
                      @endforeach
                    @endif
                  </table>
                </div>
                <div class="flex items-center justify-between text-xs text-center border-t border-t-black bc-black">
                  <h3 class="w-1/3 px-1 bg-red-500 text-white border-r border-r-black bc-black">{{ $product?->group->code }}</h3>
                  <h3 class="w-2/3 px-1 bg-red-500 @if(! $product->barcode) text-red-500 @else text-white @endif">{{ $product->barcode ?: '-' }}</h3>
                </div>
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
  <br>
  <div class="p-0">
    <table class="w-full">
      <tbody>
        <tr class="flex">
          @foreach ($pricetagGroups as $i => $group)
            <td class="flex flex-col justify-start w-10cm p-0">
              <div class="wrapper border border-black bg-white kingthings">
                <div class="max-w-10cm relative mb-[36px] border-0 p-0">
                  <h3 ref="title" class="absolute min-w-full h-[36px] flex items-center justify-center bg-red-500 text-center text-white px-2 py-3 border-b-black border-b bc-black text-lg">{{ mb_strtoupper($group->name) }}</h3>
                </div>
                <div class="px-2">
                  <div class="flex items-center justify-center px-3 pb-1 border-b border-b-red-500 bc-red">
                    <p class="text-xl font-bold" style="line-height: 1.5rem">Rp &nbsp;</p>
                    <h1 class="text-xl font-bold" style="line-height: 1.5rem">{{ $group->sample->price?->price_per_unit ? number_format($group->sample->price->price_per_unit, 0, '', '.') : 0 }}</h1>
                  </div>
                  <table class="w-full">
                    @if ($group?->variable_costs?->count() > 0)
                      @foreach ($group?->variable_costs?->sortBy('min_qty')->take(3) as $j => $item)
                        <tr class="text-black border-t border-t-red-500 bc-red">
                          <td class="variable-price text-left">Beli {{ $item?->qty }} pcs </td>
                          <td class="variable-price text-center">Rp. {{ $item?->price ? number_format($item->price, 0, '', '.') : 0 }}</td>
                          <td class="variable-price text-right">Rp. {{ ((int) $item?->price * (int) $item?->qty) ? number_format(((int) $item?->price * (int) $item?->qty), 0, '', '.') : 0 }} </td>
                        </tr>
                      @endforeach
                      @if ($group?->variable_costs->count() < 3)
                        @for ($i = 0; $i < 3 - $group?->variable_costs->count(); $i++)
                          <tr class="text-black border-t border-t-red-500 bc-red">
                            <td class="variable-price text-white"> - </td>
                          </tr>
                        @endfor
                      @endif
                    @else
                      @foreach ([0, 1, 2] as $j => $item)
                        <tr class="text-red-600 border-t border-t-red-600 bc-red">
                          <td class="variable-price text-white">-</td>
                        </tr>
                      @endforeach
                    @endif
                  </table>
                </div>
                <div class="flex items-center justify-between text-xs text-center border-t border-t-black bc-black">
                  <h3 class="w-1/3 px-1 bg-red-500 text-white">{{ $group->sample?->group->code }}</h3>
                  <h3 class="w-1/3 px-1 not-variable  capitalize border-x border-x-black">varian tidak bisa campur</h3>
                  <h3 class="w-1/3 px-1 bg-red-500 @if(! $group->barcode) text-red-500 @else text-white @endif">{{ $group->barcode ?: '-' }}</h3>
                </div>
              </div>
            </td>
            @if($loop->index % 2 === 1)
            </tr>
              @if(($loop->index % 10 === 9) || $loop->index === 9)
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