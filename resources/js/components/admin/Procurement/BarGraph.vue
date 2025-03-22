<template>
  <div class="bg-white drop-shadow-lg h-[300px] sm:col-span-2 dark:bg-custom-table">
    <!-- Render the chart only after data is available -->
    <BarGraph 
      v-if="!loading && chartData.labels.length > 0" 
      ref="barChart" 
      :data="chartData" 
      :options="chartOptions" 
    />
    <p v-else>Loading chart...</p>
  </div>
</template>

<script>
import { Bar } from 'vue-chartjs';
import {
Chart as ChartJS,
Title,
Tooltip,
Legend,
BarElement,
CategoryScale,
LinearScale
} from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale);

export default {
name: 'Barchart',
components: { BarGraph: Bar },
data() {
  // Detect dark mode
  const isDarkMode = document.documentElement.classList.contains('dark');

  // Get dark mode color from CSS variable
  const customWhite = getComputedStyle(document.documentElement)
    .getPropertyValue('--custom-white')
    .trim() || '#E5E7EB';

  // Use Tailwind's default color (e.g., blue-500)
  const defaultColor = '#365486'; // Tailwind blue-500 hex code
  const defaultTextColor ='#000';

  return {
    chartData: {
      labels: ['Loading...'],
      datasets: [
        {
          label: '20 Available Materials (overview)', 
          data: [0],
          backgroundColor: isDarkMode ? customWhite : defaultColor,
          borderColor: isDarkMode ? customWhite : defaultColor,
          borderWidth: 1
        }
      ]
    },
    chartOptions: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            color: isDarkMode ? customWhite : defaultTextColor, // Y-axis label color
          }
        },
        x: {
          ticks: {
            color: isDarkMode ? customWhite : defaultTextColor, // X-axis label color
          }
        }
      },
      plugins: {
        legend: {
          labels: {
            color: isDarkMode ? customWhite : defaultTextColor, // Legend color
          }
        }
      }
    },
    inventoryMaterials: [],
    loading: true,
  };
},
mounted() {
  console.log('Mounted lifecycle hook');
  this.loadFromLocalStorage();

  // Listen for real-time updates using Pusher
  window.Echo.private('broadcast-bargraph')
    .listen('.bargraph-updated', (data) => {
      console.log('Fetch materials:', data);
      this.inventoryMaterials = data.inventoryMaterials;
      this.saveToLocalStorage();
      this.updateChartData(); // Update chart data after broadcast
    });
},
methods: {
  saveToLocalStorage() {
    localStorage.setItem('inventoryMaterials', JSON.stringify(this.inventoryMaterials));
  },
  loadFromLocalStorage() {
    try {
      const materials = localStorage.getItem('inventoryMaterials');
      this.inventoryMaterials = materials ? JSON.parse(materials) : [];
      this.updateChartData(); // ✅ Update chart data after loading from storage
      this.loading = false;
    } catch (error) {
      console.error('Failed to load materials:', error);
      this.inventoryMaterials = [];
      this.loading = false;
    }
  },
  updateChartData() {
    if (this.inventoryMaterials.length > 0) {
      const highStockMaterials = this.inventoryMaterials
        .filter(material => material.stocks  > 0) // Adjust threshold if needed
        .sort(() => Math.random() - 0.5) // ✅ Sort randomly
        .slice(0, 20); // Limit to top 10 materials

      this.chartData.labels = highStockMaterials.map(
        material => `${material.material_name} - ${material.measurement_quantity} ${material.measurement_unit}`
      );
      this.chartData.datasets[0].data = highStockMaterials.map(
        material => material.stocks
      );

      this.$nextTick(() => {
        if (this.$refs.barChart?.chart) {
          console.log('Updating chart');
          this.$refs.barChart.chart.update(); // ✅ Use update() instead of renderChart()
        }
      });
    } else {
      this.chartData.labels = ['No data'];
      this.chartData.datasets[0].data = [0];
    }
  },
},
beforeUnmount() {
  if (this.$refs.barChart?.chart) {
    console.log('Destroying chart');
    this.$refs.barChart.chart.destroy(); // ✅ Properly destroy the chart
  }
}
};
</script>