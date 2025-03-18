<template>
    <div class="bg-white drop-shadow-lg h-[300px] sm:col-span-2">
      <!-- Render the chart only after data is available -->
      <LineGraph 
        v-if="!loading && chartData.labels.length > 0" 
        ref="lineChart" 
        :data="chartData" 
        :options="chartOptions" 
      />
      <p v-else>Loading chart...</p>
    </div>
  </template>
  
  <script>
  import { Line } from 'vue-chartjs';
  import {
    Chart as ChartJS,
    Title,
    Tooltip,
    Legend,
    LineElement,
    CategoryScale,
    LinearScale,
    PointElement
  } from 'chart.js';
  
  ChartJS.register(Title, Tooltip, Legend, LineElement, CategoryScale, LinearScale, PointElement);
  
  export default {
    name: 'Linechart',
    components: { LineGraph: Line },
    data() {
      return {
        chartData: {
          labels: ['Loading...'],
          datasets: [
            {
              label: 'Stock Levels',
              data: [], // y-axis → stock levels
              backgroundColor: 'rgba(75, 192, 192, 0.2)', // Light green
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 2,
              tension: 0.4, // Smooth curve
              pointBackgroundColor: 'rgba(75, 192, 192, 1)',
              pointBorderColor: '#fff'
            }
          ]
        },
        chartOptions: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Stock Level'
              }
            },
            x: {
              title: {
                display: true,
                text: 'Materials'
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
      window.Echo.private('broadcast-linegraph')
        .listen('.linegraph-updated', (data) => {
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
                // ✅ Show all materials (no filter)
                const sortedMaterials = this.inventoryMaterials
                .sort((a, b) => new Date(a.date) - new Date(b.date)); // Sort by date

                this.chartData.labels = sortedMaterials.map(
                material => this.formatDate(material.date )// Use date for x-axis
                );

                this.chartData.datasets[0].data = sortedMaterials.map(
                material => material.stocks // Stock levels for y-axis
                );

                this.$nextTick(() => {
                if (this.$refs.lineChart?.chart) {
                    console.log('Updating chart');
                    this.$refs.lineChart.chart.update(); // ✅ Update chart after data change
                }
                });
            } else {
                this.chartData.labels = ['No data'];
                this.chartData.datasets[0].data = [0];
            }
            },
            formatDate(date) {
                return new Date(date).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
    },
    },
    beforeUnmount() {
      if (this.$refs.lineChart?.chart) {
        console.log('Destroying chart');
        this.$refs.lineChart.chart.destroy(); // ✅ Properly destroy the chart
      }
    }
  };
  </script>