<template>
    <div class="p-8">
           <p>Mange your account setting and preferences</p>
   
               <form 
                   action="/submit-form" 
                   method="POST" 
                   class="w-full max-w-3xl mx-auto bg-gray-100 p-4 rounded-md shadow-md space-y-6 mt-5"
                   >
                   <!-- Form Header -->
                   <div class="bg-custom-blue p-4 rounded-md shadow-sm">
                       <h2 class="text-lg font-bold text-white">Edit Information</h2>
                   </div>
   
                   <!-- Section 1: Personal Information -->
                   <div class="bg-white p-4 rounded-md shadow-sm">
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                           <label for="Name" class="block text-sm font-semibold text-gray-700">Name:</label>
                           <input 
                           type="text" 
                           id="Name" 
                           name="Name" 
                           placeholder="Enter your name"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                           />
                       </div>
   
                       <div>
                           <label for="Email" class="block text-sm font-semibold text-gray-700">Email:</label>
                           <input 
                           type="email" 
                           id="Email" 
                           name="Email" 
                           placeholder="Enter your email"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                           />
                       </div>
                       </div>
                   </div>
   
                   <!-- Section 2: Employee Information -->
                   <div class="bg-white p-4 rounded-md shadow-sm">
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                           <label for="EmployeeID" class="block text-sm font-semibold text-gray-700">Employee ID:</label>
                           <input 
                           type="number" 
                           id="EmployeeID" 
                           name="EmployeeID" 
                           placeholder="Enter your ID"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                           />
                       </div>
   
                       <div>
                           <label for="Department" class="block text-sm font-semibold text-gray-700">Department/Position:</label>
                           <input 
                           type="text" 
                           id="Department" 
                           name="Department" 
                           placeholder="Enter department/position"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           />
                       </div>
                       </div>
                   </div>
   
                   <!-- Section 3: Password Management -->
                   <div class="bg-white p-4 rounded-md shadow-sm">
                       <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                       <div>
                           <label for="CurrentPassword" class="block text-sm font-semibold text-gray-700">Current Password:</label>
                           <input 
                           type="password" 
                           id="CurrentPassword" 
                           name="CurrentPassword" 
                           placeholder="Enter current password"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                           />
                       </div>
   
                       <div>
                           <label for="NewPassword" class="block text-sm font-semibold text-gray-700">New Password:</label>
                           <input 
                           type="password" 
                           id="NewPassword" 
                           name="NewPassword" 
                           placeholder="Enter new password"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                           />
                       </div>
                       </div>
   
                       <div class="mt-4">
                       <label for="ReTypeNewPass" class="block text-sm font-semibold text-gray-700">Re-Type New Password:</label>
                       <input 
                           type="password" 
                           id="ReTypeNewPass" 
                           name="ReTypeNewPass" 
                           placeholder="Re-type new password"
                           class="mt-1 w-full p-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                           required
                       />
                       </div>
                   </div>
   
                   <!-- Submit Button -->
                   <div class="flex justify-end">
                       <button 
                       type="submit" 
                       class="bg-custom-blue text-white px-6 py-2 w-full rounded-md font-semibold shadow-md"
                       >
                       Submit
                       </button>
                   </div>
                   <div class=""> 
                   <p class="text-red-500 text-sm">
                       *Atleast 8 characters long 
                   </p>
                   <p class="text-red-500 text-sm">
                       *1 lowercase letter
                   </p>
                   <p class="text-red-500 text-sm">
                       *1 uppercase letter
                   </p>
                   <p class="text-red-500 text-sm">
                       *1 special character
                   </p>
                   <p class="text-red-500 text-sm">
                       *different from your previous password
                   </p>
               </div>
                   </form>
   
           
   
       </div>
   
   </template>
   
   <script>
   export default {
     
   }
   </script>