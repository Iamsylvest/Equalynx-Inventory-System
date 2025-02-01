<template>
    <div class="mt-5">
           <!-- Delivery Receipt ActivityLogs -->
        <table class="table-auto border-collapse shadow-lg w-full">
        <thead class="h-14 bg-gray-100">
          <tr class="bg-custom-blue text-white">
            <th class="px-6 py-4 font-bold text-center">Time Stamp</th>
            <th class="px-6 py-4 font-bold text-center">User</th>
            <th class="px-6 py-4 font-bold text-center">Role</th>
            <th class="px-6 py-4 font-bold text-center">Activity</th>
            <th class="px-6 py-4 font-bold text-center">DR#</th>
          </tr>
        </thead>
        <tbody class="text-center">
          <tr class="bg-white">
            <td class="px-6 py-4">11/25/2024</td>
            <td class="px-6 py-4">12345</td>
            <td class="px-6 py-4">Geryk Martin Bubutan</td>
            <td class="px-6 py-4">Mcdo</td>
            <td class="px-6 py-4">(Manager)</td>
          </tr>
          <tr class="bg-gray-100">
            <td class="px-6 py-4">11/25/2024</td>
            <td class="px-6 py-4">12345</td>
            <td class="px-6 py-4">Geryk Martin Bubutan</td>
            <td class="px-6 py-4">Mcdo</td>
            <td class="px-6 py-4">(Manager)</td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  export default {
    // No need for showTable prop anymore
  };
  </script>