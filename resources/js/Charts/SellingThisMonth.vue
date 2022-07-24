<script setup>
import { use } from 'echarts/core';
import { CanvasRenderer } from 'echarts/renderers';
import { LineChart } from 'echarts/charts';
import {
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
  GridComponent,
} from 'echarts/components';
import VChart from 'vue-echarts';
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { id } from 'date-fns/locale';

const { date, render } = defineProps({
  date: Object,
  render: Boolean,
})

use([
  GridComponent,
  CanvasRenderer,
  LineChart,
  TitleComponent,
  TooltipComponent,
  LegendComponent,
  VisualMapComponent,
]);

const option = ref({
  xAxis: {
    type: 'category',
    boundaryGap: false
  },
  yAxis: {
    type: 'value',
    boundaryGap: [0, '30%']
  },
  visualMap: {
    type: 'piecewise',
    show: false,
    dimension: 0,
    seriesIndex: 0,
    pieces: [
      {
        gt: 1,
        lt: 2,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 3,
        lt: 4,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 5,
        lt: 6,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 7,
        lt: 8,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 9,
        lt: 10,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 11,
        lt: 12,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 13,
        lt: 14,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 15,
        lt: 16,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 17,
        lt: 18,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 19,
        lt: 20,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 21,
        lt: 22,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 23,
        lt: 24,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 25,
        lt: 26,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 27,
        lt: 28,
        color: 'rgba(0, 0, 180, 0.4)'
      },

      {
        gt: 29,
        lt: 30,
        color: 'rgba(0, 0, 180, 0.4)'
      },
    ]
  },
  series: [
    {
      type: 'line',
      smooth: 0,
      // symbol: 'none',
      lineStyle: {
        color: '#5470C6',
        width: 2
      },
      areaStyle: {},
      data: [],
    }
  ]
})

const fetch = async () => {
  try {
    const response = await axios.post(route('api.selling'), date)
    
    option.value.series[0].data = Object.keys(response.data).sort().map(key => [key, response.data[key]])
  } catch (e) {
    console.log(e)
  }
}

onMounted(fetch)
</script>

<template>
  <div class="bg-white rounded-md">
    <h1 class="text-3xl font-semibold px-4 pt-2">
      Grafik penjualan bulan {{ id.localize.month(date.month) }} {{ date.year }}
    </h1>
    <VChart class="h-96 rounded-md" :option="option" />
  </div>
</template>
