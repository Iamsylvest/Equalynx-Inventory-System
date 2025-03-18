<template>
    <div class="">
        <header class="p-4 flex justify-center  md:flex md:justify-between md:items-center ">
        
            <div class="hidden md:flex md:space-x-4">
            <Notification />
            <Profile />
             </div>
        </header>
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4 w-full h-auto">
                <!-- Low Stock Modal -->
                <LowStockModal
                    :showLowStockModal="showLowStockModal"
                    @openLowStockModal="openLowStockModal"
                    @lowStockcloseModal="closeLowStockModal"
                    class="flex items-center justify-center bg-white shadow-md rounded-xl p-4"
                />

                <!-- High Stock Modal -->
                <HighStockModal
                    :showHighStockModal="showHighStockModal"
                    @openHighStockModal="openHighStockModal"
                    @closeHighStockModal="closeHighStockModal"
                    class="flex items-center justify-center bg-white shadow-md rounded-xl p-4"
                />

                <!-- Out of Stock Modal -->
                <OutStockModal
                    :showOutStockModal="showOutStockModal"
                    @openOutStockModal="openOutStockModal"
                    @outStockcloseModal="closeOutStockModal"
                    class="flex items-center justify-center bg-white shadow-md rounded-xl p-4"
                />

                <!-- Slow Moving Modal -->
                <SlowMovingModal
                    :showSlowMovingModal="showSlowMovingModal"
                    @openSlowMovingModal="openSlowMovingModal"
                    @closeSlowMovingkModal="closeSlowMovingModal"
                    class="flex items-center justify-center bg-white shadow-md rounded-xl p-4"
                />

                <!-- Bar Graph -->
                <div class="col-span-2 bg-white shadow-md rounded-xl p-4">
                    <BarGraph />
                </div>

                <!-- Line Graph -->
                <div class="col-span-2 bg-white shadow-md rounded-xl p-4">
                    <LineGraph />
                </div>
                </div>
    </div>

</template>
<script>
import LowStockModal from '@/components/admin/Procurement/LowStockModal.vue'
import HighStockModal from '@/components/admin/Procurement/HighStockModal.vue'
import OutStockModal from '@/components/admin/Procurement/OutStockModal.vue'
import SlowMovingModal from '@/components/admin/Procurement/SlowMovingModal.vue'
import BarGraph from '@/components/admin/Procurement/BarGraph.vue'
import LineGraph from '@/components/admin/Procurement/LineGraph.vue'
export default {
    components: {
    LowStockModal,
    HighStockModal,
    OutStockModal,
    SlowMovingModal,
    BarGraph,
    LineGraph,
},
    data(){
        return{
            showLowStockModal: false,
            showHighStockModal: false,
            showOutStockModal: false,
            showSlowMovingModal: false,
        };
    },
    methods: {
        closeLowStockModal() {
            this.showLowStockModal = false;
        },
        openLowStockModal(){
            this.showLowStockModal = true;
        },
        closeHighStockModal() {
            this.showHighStockModal = false;
        },
        openHighStockModal(){
            this.showHighStockModal = true;
        },
        closeOutStockModal() {
            this.showOutStockModal = false;
        },
        openOutStockModal(){
            this.showOutStockModal = true;
        },
        closeSlowMovingModal() {
            this.showSlowMovingModal = false;
        },
        openSlowMovingModal(){
            this.showSlowMovingModal = true;
        }
        
    }
}


</script>