<script setup>
import { getCurrentInstance, nextTick, onMounted, ref, Teleport } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'
import Select from '@vueform/multiselect'
import { useForm } from '@inertiajs/inertia-vue3'
import Swal from 'sweetalert2'
import axios from 'axios'

const self = getCurrentInstance()
const products = ref([])

const form = useForm({
  cash: 0,
})

const temp = useForm({
  transactions: [],
  pay: 0,
})

const current = useForm({
  product_id: 0,
  qty: 1,
  type: 'unit',
})

const transactions = ref([])

const outOfStock = (product, type) => {
  const stock = product['stock_' + type]

  return Swal.fire({
      text: `Stok ${type} product "${product.name}" ${stock > 0 ? 'hanya tersedia ' + stock : 'tidak tersedia'}`,
      icon: stock > 0 ? 'info' : 'warning',
      showCloseButton: true,
      timer: 5000,
    })
}

const add = () => {
  if (!current.product_id) return

  const has = transactions.value.find(t => {
    return t.product_id === current.product_id && t.type === current.type
  })

  const product = products.value.find(p => p.id === current.product_id)

  if (!product)
    return

  if (product['stock_' + current.type] < 1)
    return outOfStock(product, current.type)

  if (has) {
    if (product['stock_' + current.type] < current.qty)
      return outOfStock(product, current.type)
      
    has.qty += current.qty
    product['stock_' + current.type] -= current.qty
    current.reset()
    self.refs.product.focus()
    self.refs.product.close()
  } else {
    if (product['stock_' + current.type] > 0 && current.qty <= product['stock_' + current.type]) {
      if (current.qty === '') current.qty = 1
      transactions.value.unshift(current.data())
      product['stock_' + current.type] -= current.qty
      current.reset()
      self.refs.product.focus()
      self.refs.product.close()
    } else {
      outOfStock(product, current.type)
    }
  }
}

const getPriceByTransaction = transaction => {
  const product = products.value.find(p => p.id === transaction.product_id)
  
  if (!product) return 0
  if (transaction.type === 'unit') return product.price.price_per_unit
  if (transaction.type === 'box') return product.price.price_per_box
  if (transaction.type === 'carton') return product.price.price_per_carton
}

const grandTotal = () => {
  if (!transactions.value.length) return 0

  return transactions.value.reduce((total, transaction) => getPriceByTransaction(transaction) * transaction.qty + total, 0)
}

const remove = async transaction => {
  // const response = await Swal.fire({
  //   title: 'Masukan kode',
  //   icon: 'question',
  //   showCloseButton: true,
  //   showCancelButton: true,
  //   input: 'password',
  //   inputPlaceholder: 'Masukan kode',
  //   inputValidator: async code => {
  //     try {
  //       const response = await axios.post(route('api.compare'), {
  //         code,
  //       })
  //     } catch (e) {
  //       return 'Kode salah'
  //     }
  //   },
  // })

  // if (!response.isConfirmed)
  //   return

  const product = find(transaction)
  product['stock_' + transaction.type] += transaction.qty
  transactions.value = transactions.value.filter(t => t.product_id === transaction.product_id ? t.type !== transaction.type : true)
}

const reformat = e => {
  const value = e.target.value
  var val = new String(value),
      replaced = val.replace(/[^,\d]/g, '').toString(),
      split = replaced.split(','),
      remaining = split[0].length % 3,
      result = split[0].substring(0, remaining),
      thousand = split[0].substring(remaining).match(/\d{3}/gi),
      separator

  if (thousand) {
    separator = remaining ? '.' : '';
    result += separator + thousand.join('.');
  }

  result = split[1] != undefined ? result + ',' + split[1] : result;

  e.target.value = `Rp ${result}`
  form.cash = parseFloat(result.replaceAll('.', '').replaceAll(',', '.'))
}

const submit = () => {
  if (temp.processing) {
    return
  }

  if (form.cash < grandTotal()) {
    return Swal.fire({
      title: 'Peringatan',
      text: 'Jumlah bayar harus melebihi dari total pembelian',
      icon: 'warning',
    }).then(response => {
      self.refs?.cash?.focus()
    })
  }

  if (transactions.value.length === 0) {
    return Swal.fire({
      title: 'Peringatan',
      text: 'Tidak ada barang yang akan dijual',
      icon: 'warning',
    }).then(response => {
      self.refs?.product?.focus()
    })
  }

  return Swal.fire({
    title: 'Akhiri proses transaksi?',
    icon: 'question',
    showCancelButton: true,
  }).then(({isConfirmed}) => {
    if (isConfirmed) {
      temp.transactions = transactions.value
      temp.pay = form.cash
      temp.post(route('transaction.store'), {
        onSuccess: () => {
          form.reset()
          current.reset()
          transactions.value = []
          
          self.refs.cash && (self.refs.cash.value = '')
          fetch()

          print()
        },
      })
    }
  })
}

const print = async () => {
  try {
    const iframe = document.createElement('iframe')
    iframe.src = route('api.transaction.latest.print')
    iframe.style.width = '0'
    iframe.style.height = '0'
    iframe.onload = () => Swal.close()
    document.body.appendChild(iframe)
  } catch (e) {
    const response = await Swal.fire({
      title: 'Tidak dapat mencetak',
      text: 'Apakah anda ingin mencoba kembali?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })
    response.isConfirmed && print()
  }
}

const getStockOf = transaction => {
  const product = products.value.find(p => p.id === transaction.product_id)

  if (product) {
    return product['stock_' + transaction.type]
  }

  return 0
}

const find = transaction => products.value.find(p => p.id === transaction.product_id)

const increment = transaction => {
  const product = find(transaction)
  if (getStockOf(transaction) > 0) {
    transaction.qty += 1
    product['stock_' + transaction.type] -= 1
  } else {
    outOfStock(product, transaction.type)
  }
}

const decrement = transaction => {
  const product = find(transaction)

  if (transaction.qty > 0) {
    transaction.qty -= 1
    product['stock_' + transaction.type] += 1
  }
}

const fetch = async () => {
  try {
    const response = await axios.get(route('api.product.where.has.stock'))
    products.value = response.data
    self.refs.product.focus()
    self.refs.product.close()
    Swal.close()
  } catch (e) {
    const response = await Swal.fire({
      title: 'Pengambilan data produk gagal',
      text: 'Apakah anda ingin mencoba lagi?',
      icon: 'question',
      showCancelButton: true,
      showCloseButton: true,
    })

    response.isConfirmed && fetch()
  }
}

onMounted(() => {
  fetch()

  window.addEventListener('keyup', e => {
    if (e.key === 'F1') {
      self?.refs?.cash?.focus()
      form.cash = ''
    }

    if (e.key === 'F2') {
      self?.refs?.qty?.focus()
      current.qty = ''
    }

    if (e.key === 'Escape') {
      self?.refs?.product?.focus()
    }
  })
});
</script>

<style src="@vueform/multiselect/themes/default.css"></style>

<template>
  <AppLayout title="Transaction">
    <div class="flex space-x-2">
      <form @submit.prevent="add" class="flex flex-col space-y-2 bg-white border rounded-md p-2 shadow w-full">
        <div class="flex items-center space-x-2">
          <label for="name" class="w-1/4">Barcode / Kode</label>
          <Select
            v-model="current.product_id"
            :options="products.map(p => ({
              label: `${p.code ? p.code + '-' : ''} ${p.barcode} - ${p.name}`,
              value: p.id,
            }))"
            :searchable="true"
            required 
            ref="product"
            noOptionsText="Mohon tunggu..."
            @change="nextTick(add)"
          />
        </div>

        <div class="flex items-center space-x-2">
          <label for="name" class="w-1/4 basis-52">Qty</label>
          <input v-model="current.qty" ref="qty" type="number" name="qty" class="w-full bg-transparent border border-slate-300 rounded-md" min="1">
          
          <Select
            v-model="current.type"
            :options="[
              { label: 'satuan', value: 'unit' },
              // { label: 'box', value: 'box' },
              // { label: 'carton', value: 'carton' },
            ]"
            :searchable="true"
            class="max-w-[8rem]"
            required />
          <!-- <i @click.prevent="current.qty > 1 && (current.qty -= 1)" class="bg-slate-50 hover:bg-white transition-all ease-in-out duration-100 border rounded-md p-3 bx bx-minus cursor-pointer"></i>
          <i @click.prevent="current.qty += 1" class="bg-slate-50 hover:bg-white transition-all ease-in-out duration-100 border rounded-md p-3 bx bx-plus cursor-pointer"></i> -->
        </div>

        <div class="flex items-center justify-end">
          <button type="submit" class="bg-green-600 rounded-md px-3 py-1 text-sm text-white">
            <div class="flex items-center space-x-1">
              <i class="bx bx-plus"></i>
              <p class="font-semibold lowercase first-letter:capitalize">tambah</p>
            </div>
          </button>
        </div>
      </form>

      <div class="flex flex-col items-center justify-center space-y-4 bg-white border rounded-md pt-2 px-8 shadow w-full">
        <div class="flex items-center justify-between w-full">
          <p class="text-5xl font-bold">Rp</p>
          <h1 class="text-5xl font-bold">{{ grandTotal().toLocaleString('id') }}</h1>
        </div>
      </div>
    </div>

    <div class="flex flex-col space-y-2 p-2 bg-white rounded-md">
      <div class="max-h-96 overflow-auto border rounded-md">
        <table class="border-collapse border w-full">
          <thead class="bg-slate-50 uppercase sticky top-0 left-0">
            <tr>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" rowspan="2">no</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" rowspan="2">nama</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" rowspan="2">qty</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" rowspan="2">satuan</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" colspan="2">harga</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap" rowspan="2">aksi</th>
            </tr>

            <tr>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap">satuan</th>
              <th class="border px-3 py-2 text-center font-semibold whitespace-nowrap">subtotal</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(transaction, i) in transactions" :key="i" class="hover:bg-slate-100 transition-all duration-100 ease-in-out">
              <td class="border py-1 text-center">{{ i + 1 }}</td>
              <td class="border py-1 text-center">{{ find(transaction)?.name }}</td>
              <td class="border py-1 text-center">
                <div class="flex items-center justify-center space-x-1">
                  <i @click.prevent="decrement(transaction)" class="p-3 bg-slate-50 rounded-md bx bx-minus cursor-pointer"></i>
                  <p class="bg-slate-50 rounded-md p-2 border-0 appearance-none outline-none cursor-default">{{ transaction.qty }}</p>
                  <i @click.prevent="increment(transaction)" class="p-3 bg-slate-50 rounded-md bx bx-plus cursor-pointer"></i>
                </div>
              </td>
              <td class="border py-1 px-3 text-center">{{ transaction.type === 'unit' ? 'satuan' : transaction.type }}</td>
              <td class="border py-1 px-3 text-right">{{ rupiah(getPriceByTransaction(transaction)) }}</td>
              <td class="border py-1 px-3 text-right">{{ rupiah(getPriceByTransaction(transaction) * transaction.qty) }}</td>
              <td class="border py-1 px-3 text-center">
                <button @click.prevent="remove(transaction)" class="bg-red-600 rounded-md px-3 py-2 text-white">
                  <i class="bx bx-trash"></i>
                </button>
              </td>
            </tr>

            <tr v-if="!transactions.length">
              <td colspan="7" class="border py-4 text-center font-semibold text-3xl">Belum ada produk yang ditambahkan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="flex items-start   justify-between space-x-4">
      <div class="bg-white w-full max-w-sm rounded-md p-4 flex flex-col space-y-4">
        <h4 class="text-lg">Shortcut</h4>
        <ul>
          <li>Esc : Product</li>
          <li>F1  : Cash</li>
          <li>F2  : Quantity</li>
        </ul>
      </div>
      <form @submit.prevent="submit" class="bg-white w-full max-w-sm rounded-md p-4 flex flex-col space-y-4">
        <div class="flex items-center justify-between space-x-2">
          <label class="lowercase first-letter:capitalize">cash</label>
          <input ref="cash" @input.prevent="reformat" type="text" class="bg-transparent rounded-md placeholder:capitalize text-right" placeholder="cash" required>
        </div>

        <div class="flex items-center justify-between space-x-2">
          <label class="lowercase first-letter:capitalize">kembalian</label>
          <input type="text" class="bg-slate-50 rounded-md placeholder:capitalize text-right" placeholder="kembalian" :value="rupiah(form.cash - grandTotal())" disabled>
        </div>

        <div class="flex items-center justify-end space-x-2">
          <button type="submit" class="bg-blue-600 rounded-md px-3 py-1 text-sm text-white font-semibold" :class="temp.processing && 'cursor-not-allowed bg-red-500'" :disabled="temp.processing">
            <div class="flex items-center space-x-1">
              <i class="bx bx-check"></i>
              <p class="lowercase first-letter:capitalize">checkout</p>
            </div>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>

  <Teleport v-if="temp.processing" to="body">
    <div class="fixed top-0 left-0 w-full h-full bg-gray-800 opacity-50 z-20"></div>
  </Teleport>
</template>