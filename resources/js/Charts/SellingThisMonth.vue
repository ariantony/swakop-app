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
    dimension: 1,
    seriesIndex: 0,
    pieces: [
      // {
      //   gt: 0,
      //   lt: 1,
      //   color: 'rgba(0, 0, 180, 0.4)'
      // },
      // {
      //   gt: 2,
      //   lt: 3,
      //   color: 'rgba(0, 0, 180, 0.4)'
      // },
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
    <VChart class="h-96 rounded-md" :option="option" />
  </div>
</template>
