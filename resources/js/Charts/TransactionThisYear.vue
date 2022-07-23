<script setup>
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { BarChart, LineChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
  GridComponent,
  ToolboxComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { id } from 'date-fns/locale';
import { rupiah } from '../common';

const { date, render } = defineProps({
  date: Object,
  render: Boolean,
})

use([
  GridComponent,
  CanvasRenderer,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
  ToolboxComponent,
  BarChart,
]);

const option = ref({
  tooltip: {
    trigger: 'axis',
    axisPointer: {
      type: 'cross',
      crossStyle: {
        color: '#999'
      }
    }
  },
  toolbox: {
    feature: {
      magicType: { show: true, type: ['line', 'bar'] },
      saveAsImage: { show: true }
    }
  },
  legend: {
    data: ['Pembelian', 'Penjualan']
  },
  xAxis: [
    {
      type: 'category',
      data: [
        id.localize.month(0),
        id.localize.month(1),
        id.localize.month(2),
        id.localize.month(3),
        id.localize.month(4),
        id.localize.month(5),
        id.localize.month(6),
        id.localize.month(7),
        id.localize.month(8),
        id.localize.month(9),
        id.localize.month(10),
        id.localize.month(11),
      ],
      axisPointer: {
        type: 'shadow'
      }
    }
  ],
  yAxis: [
    {
      type: 'value',
      name: 'Precipitation',
      min: 0,
      // interval: 50,
      axisLabel: {
        formatter: value => rupiah(value)
      }
    },
  ],
  series: [
    {
      name: 'Pembelian',
      type: 'bar',
      tooltip: {
        valueFormatter: function (value) {
          return rupiah(value);
        }
      },
      data: []
    },
    {
      name: 'Penjualan',
      type: 'bar',
      tooltip: {
        valueFormatter: function (value) {
          return rupiah(value);
        }
      },
      data: []
    },
  ]
})

const fetch = async () => {
  try {
    const response = await axios.post(route('api.profit'))

    option.value.series[0].data = response.data.map(t => t.buy)
    option.value.series[1].data = response.data.map(t => t.sell)
  } catch (e) {
    console.log(e)
  }
}

onMounted(fetch)
</script>

<template>
  <div class="bg-white rounded-md">
    <h1 class="text-xl font-semibold px-4 pt-2">
      Grafik pembelian dan penjualan tahun {{ date.year }}
    </h1>
    <VChart class="h-96 rounded-md" :option="option" />
  </div>
</template>
