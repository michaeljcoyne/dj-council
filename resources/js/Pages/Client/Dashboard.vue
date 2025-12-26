<template>
    <div class="min-h-screen bg-gray-900">
        <!-- Top Navigation -->
        <header class="bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <span class="text-white font-bold">DJ</span>
                            </div>
                            <span class="ml-2 text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
                DJ Council
              </span>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="ml-3 relative">
                            <div>
                                <button class="flex text-sm rounded-full focus:outline-none">
                                    <span class="sr-only">Open user menu</span>
                                    <div class="w-8 h-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold">
                                        C
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-800 min-h-screen">
                <div class="px-4 py-6">
                    <nav class="space-y-1">
                        <a href="/client/dashboard" class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg">
                            Dashboard
                        </a>
                        <a href="/client/djs" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            Find DJs
                        </a>
                        <a href="/client/bookings" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            My Bookings
                        </a>
                        <a href="/client/playlists" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                            My Playlists
                        </a>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 p-6">
                <div class="max-w-7xl mx-auto">
                    <h1 class="text-3xl font-bold text-white mb-6">Client Dashboard</h1>

                    <!-- Stats Overview -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <div class="bg-gray-800 p-6 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-600 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-white">Upcoming Events</h2>
                                    <p class="text-2xl font-bold text-white">2</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-800 p-6 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-pink-600 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-white">Playlists</h2>
                                    <p class="text-2xl font-bold text-white">3</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-800 p-6 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-indigo-600 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-white">Saved DJs</h2>
                                    <p class="text-2xl font-bold text-white">5</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-800 p-6 rounded-lg shadow">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-600 rounded-lg">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold text-white">Completed Events</h2>
                                    <p class="text-2xl font-bold text-white">8</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Bookings -->
                    <div class="mb-8">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-bold text-white">Upcoming Bookings</h2>
                            <a href="/client/bookings" class="text-sm text-purple-400 hover:text-purple-300">View All</a>
                        </div>

                        <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                            <ul class="divide-y divide-gray-700">
                                <li class="p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="mb-2 sm:mb-0">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <p class="ml-2 font-medium text-white">Corporate Event</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-400">Jun 15, 2025 • 8:00 PM - 12:00 AM</p>
                                        </div>

                                        <div class="mb-2 sm:mb-0">
                                            <p class="text-sm font-medium text-white">DJ Pulse</p>
                                            <p class="text-sm text-gray-400">Skyline Rooftop</p>
                                        </div>

                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-xs font-medium text-green-400 bg-green-900 rounded-full">Confirmed</span>
                                            <a href="/client/bookings/1" class="ml-4 text-purple-400 hover:text-purple-300">Details</a>
                                        </div>
                                    </div>
                                </li>

                                <li class="p-4">
                                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                        <div class="mb-2 sm:mb-0">
                                            <div class="flex items-center">
                                                <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                <p class="ml-2 font-medium text-white">Wedding Reception</p>
                                            </div>
                                            <p class="mt-1 text-sm text-gray-400">Jul 10, 2025 • 9:00 PM - 2:00 AM</p>
                                        </div>

                                        <div class="mb-2 sm:mb-0">
                                            <p class="text-sm font-medium text-white">DJ Vinyl Queen</p>
                                            <p class="text-sm text-gray-400">The Grand Hall</p>
                                        </div>

                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-xs font-medium text-green-400 bg-green-900 rounded-full">Confirmed</span>
                                            <a href="/client/bookings/2" class="ml-4 text-purple-400 hover:text-purple-300">Details</a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Playlists & Featured DJs -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Recent Playlists -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-white">Recent Playlists</h2>
                                <a href="/client/playlists" class="text-sm text-purple-400 hover:text-purple-300">View All</a>
                            </div>

                            <div class="grid grid-cols-1 gap-4">
                                <div class="bg-gray-800 rounded-lg shadow p-5">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-white">Summer Party Mix</h3>
                                        <div class="p-1 text-pink-500 bg-pink-500/10 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-400">
                                        <p>15 songs • 52 min</p>
                                    </div>
                                    <div class="mt-4">
                                        <a href="/client/playlists/1" class="text-sm text-purple-400 hover:text-purple-300">View Playlist</a>
                                    </div>
                                </div>

                                <div class="bg-gray-800 rounded-lg shadow p-5">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-medium text-white">Wedding Dance Floor</h3>
                                        <div class="p-1 text-pink-500 bg-pink-500/10 rounded-full">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="mt-4 text-sm text-gray-400">
                                        <p>18 songs • 1h 05min</p>
                                    </div>
                                    <div class="mt-4">
                                        <a href="/client/playlists/3" class="text-sm text-purple-400 hover:text-purple-300">View Playlist</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Featured DJs -->
                        <div>
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-white">Featured DJs</h2>
                                <a href="/client/djs" class="text-sm text-purple-400 hover:text-purple-300">Browse All</a>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                                    <div class="h-32 bg-gray-700 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-500">DJ Image</span>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-medium text-white">DJ Pulse</h3>
                                        <p class="text-sm text-purple-400">House & EDM</p>
                                        <div class="flex items-center mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-1 text-sm text-gray-300">4.9</span>
                                        </div>
                                        <div class="mt-4">
                                            <a href="/client/djs/1" class="text-sm text-purple-400 hover:text-purple-300">View Profile</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                                    <div class="h-32 bg-gray-700 flex items-center justify-center">
                                        <span class="text-2xl font-bold text-gray-500">DJ Image</span>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="font-medium text-white">DJ Vinyl Queen</h3>
                                        <p class="text-sm text-purple-400">Disco & Funk</p>
                                        <div class="flex items-center mt-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span class="ml-1 text-sm text-gray-300">4.8</span>
                                        </div>
                                        <div class="mt-4">
                                            <a href="/client/djs/2" class="text-sm text-purple-400 hover:text-purple-300">View Profile</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="mt-8">
                        <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <a href="/client/djs" class="bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white rounded-lg p-6 text-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <span class="font-medium">Find a DJ</span>
                            </a>

                            <a href="/client/playlists/create" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg p-6 text-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                <span class="font-medium">Create Playlist</span>
                            </a>

                            <a href="/client/venues/create" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-lg p-6 text-center transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span class="font-medium">Add Venue</span>
                            </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<script>
export default {
    // Add component logic here if needed
}
</script>
