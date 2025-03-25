<template>
            <!-- <div class="space-x-4 flex flex-row">
                <label for="" class="leading-">Change Threshold:</label>

                <input  type="number" v-model="threshold" class="dark:bg-custom-table border rounded-lg p-2"/><br>
                <button @click="updateThresholdInventory" class="relative p-2 px-20 border rounded-lg bg-custom-blue text-custom-white  dark:bg-custom-table">Save</button>

                <div>
                    <p v-for="(threshold, index) in fetchThreshold" :key="index"></p>
                        <p>{{ threshold.threshold }}</p>
                </div>
            </div> -->

            <!-- <div class="flex md:flex-row space-x-4">
                <div class="ml-2">
                    <p class="leading-[40px]"> Change Threshold: </p>
                </div>

                <div>
                    <input  type="number" v-model="threshold" class="dark:bg-custom-table border rounded-lg p-2"/>
                    <button @click="updateThresholdInventory" class="relative p-2 ml-2 px-20 border rounded-lg bg-custom-blue text-custom-white  dark:bg-custom-table">Save</button>
                </div>

                <div>
                    <p v-for="(threshold, index) in fetchThreshold" :key="index"></p>
                        <p>{{ threshold.threshold}}</p>
                </div>
            </div> -->

            <div class="sm:flex sm:flex-row sm:space-x-4">
                <div>
                    <p class="sm:m-2 md:m-2"> Change Threshold: </p>
                </div>

                <div class="sm:flex md:flex-row md:space-x-4">
                    <div class="mt-5 sm:mt-0 md:mt-0">
                        <input  type="number" v-model="threshold" class="dark:bg-custom-table border rounded-lg p-2"/>
                    </div>

                    <div class="mt-5 sm:mt-0 md:mt-0">
                        <button @click="updateThresholdInventory" class="relative p-2 px-20 border rounded-lg bg-custom-blue text-custom-white  dark:bg-custom-table">Save</button>
                    </div>
                </div>
            </div>

            


</template>
<script>
import axios from 'axios';
import Swal from 'sweetalert2';

export default {
    data(){
        return{
            threshold: '',
        }
    },
    mounted(){
        this.fetchThreshold()
    },
    methods:{

    async updateThresholdInventory(){
     try{
        const response = await axios.post('/api/settings/threshold', {
            threshold: this.threshold, // wrap the payload inside {}
        });
        console.log("Threshold updated successfully", response.data);

        localStorage.setItem('threshold', this.threshold);

        Swal.fire({
            title: 'Success',
            text: 'Threshold updated successfully!',
            icon : 'success'
        });
     } catch(error){
        console.error("Error updating threshold", error.response?.data || error.message);
        Swal.fire({
            title: 'Error',
            text: 'Failed to update threshold',
            icon: 'error'
        });
     }
},
     async fetchThreshold(){
        try{
            const localThreshold = localStorage.getItem('threshold');
            if (localThreshold) {
                this.threshold = parseInt(localThreshold);
            } else { 
                    // Fetch from API if not in local storage
                const response = await axios.get('/api/settings/threshold');
                console.log("Current Threshold Fetch Successfully");
                this.threshold = response.data.threshold;
                localThreshold.setItem('threshold', this.threshold);  // Save to local storage
            } 
            
        } catch(error){
            console.error("Error fetching current threshold", error.response?.data || error.message);
        }
     }

    }

};
</script>