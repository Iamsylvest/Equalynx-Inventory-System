<template>
    <div class="mt-16">
          <table class="table-auto w-full border-collapse mt-1 shadow-lg">
        <thead class="h-14">
          <tr class="bg-custom-blue text-white">
            <th class="px-4 py-2 border-0 text-center font-bold">Username</th>
            <th class="px-4 py-2 border-0 text-center font-bold">Role</th>
            <th class="px-4 py-2 border-0 text-center font-bold">Date Added</th>
            <th class="px-4 py-2 border-0 text-center font-bold">Status</th>
            <th class="px-4 py-2 border-0 text-center font-bold">Action</th>
          </tr>
        </thead>
        <tbody>
          <tr class="bg-white">
            <td class=" text-center px-4 py-2 border-0">
              <div class="items-center justify-center space-x-3">
                <span class="font-bold">JohnDoe</span> <br>
                <span class="text-xs">Hotdog@gmail.com</span>
              </div>
            </td>
            <td class="text-center px-4 py-2 border-0">Admin</td>
            <td class="text-center px-4 py-2 border-0">2024-01-01</td>
            <td class="text-center px-4 py-2 border-0">Active</td>
            <td class="text-center px-4 py-2 border-0">
              <button class="text-gray-500 hover:underline w-full sm:w-auto">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
              </button>
            </td>
          </tr>
          <!-- Add other table rows here -->
        </tbody>
      </table>
    </div>
  </template>
<script>

</script>