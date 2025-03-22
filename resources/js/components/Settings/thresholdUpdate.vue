<template>
            <div class="space-x-28 space-y-5 flex flex-row">
                <label for="">Change Threshold:</label>
              <div class="space-y-5 relative top-[-24px]">
                <input  type="number" v-model="threshold" class="dark:bg-custom-table border rounded-lg p-2"/><br>
                <button @click="updateThresholdInventory" class="relative p-2 px-20 border rounded-lg dark:bg-custom-table">Save</button>
              </div>
            </div>

            <div>
                <p v-for="(threshold, index) in fetchThreshold" :key="index"></p>
                    <p>{{ threshold.threshold }}</p>
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