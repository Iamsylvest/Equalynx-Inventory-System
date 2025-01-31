<template>
  <div class="mt-5">
    <table class="table-auto border-collapse shadow-lg w-full">
      <thead class="h-14 bg-gray-100">
        <tr class="bg-custom-blue text-white">
          <th class="px-6 py-4 font-bold text-center">Date</th>
          <th class="px-6 py-4 font-bold text-center">DR#</th>
          <th class="px-6 py-4 font-bold text-center">Name</th>
          <th class="px-6 py-4 font-bold text-center">Project Name</th>
          <th class="px-6 py-4 font-bold text-center">Approved by</th>
          <th class="px-6 py-4 font-bold text-center">Status</th>
          <th class="px-4 py-2 border-0 font-bold text-center">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr class="bg-white border-b">
          <td class="px-6 py-4 text-center">11/25/2024</td>
          <td class="px-6 py-4 text-center">12345</td>
          <td class="px-6 py-4 text-center">Sylvest Madelo</td>
          <td class="px-6 py-4 text-center">KFC</td>
          <td class="px-6 py-4 text-center">(Manager)</td>
          <td class="px-6 py-4 text-center">Pending</td>
          <td class="text-center px-4 py-2 border-0">
            <button class="text-gray-500 hover:text-custom-blue hover:underline w-full sm:w-auto">
              <!-- Icon here -->
            </button>
          </td>
        </tr>
        <tr class="bg-gray-100 border-b">
          <td class="px-6 py-4 text-center">11/25/2024</td>
          <td class="px-6 py-4 text-center">12345</td>
          <td class="px-6 py-4 text-center">Geryk Martin Bubutan</td>
          <td class="px-6 py-4 text-center">Mcdo</td>
          <td class="px-6 py-4 text-center">(Manager)</td>
          <td class="px-6 py-4 text-center">Pending</td>
          <td class="text-center px-4 py-2 border-0">
            <button class="text-gray-500 hover:text-custom-blue hover:underline w-full sm:w-auto">
              <!-- Icon here -->
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {};
</script>