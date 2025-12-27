<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    stats: Object,
    upcomingBookings: Array,
    recentPlaylists: Array,
    featuredDJs: Array,
});
</script>

<template>
    <div class="min-h-screen bg-black">
        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden md:block w-64 bg-gray-900 min-h-screen border-r border-gray-800">
                <div class="px-4 py-6">
                    <div class="flex items-center space-x-2 mb-6 px-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-white font-bold">DJ</span>
                        </div>
                        <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">DJ Council</span>
                    </div>
                    <nav class="space-y-1">
                        <Link href="/client/dashboard" class="flex items-center px-4 py-2 text-white bg-gray-800 rounded-lg">
                            Dashboard
                        </Link>
                        <Link href="/client/djs" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            Find DJs
                        </Link>
                        <Link href="/client/bookings" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            My Bookings
                        </Link>
                        <Link href="/client/playlists" class="flex items-center px-4 py-2 text-gray-400 hover:text-white hover:bg-gray-800 rounded-lg transition-colors">
                            My Playlists
                        </Link>
                    </nav>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="flex-1 bg-gradient-to-b from-gray-900 to-black">
                <div class="p-6">
                    <div class="max-w-7xl mx-auto">
                        <h1 class="text-3xl font-bold text-white mb-6">Client Dashboard</h1>

                        <!-- Stats Overview -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                            <Link href="/client/bookings?status=upcoming" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 p-6 rounded-lg shadow hover:bg-gray-800/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-3 bg-purple-600 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-lg font-semibold text-white">Upcoming Events</h2>
                                        <p class="text-2xl font-bold text-white">{{ stats?.upcomingEvents || 0 }}</p>
                                    </div>
                                </div>
                            </Link>

                            <Link href="/client/playlists" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 p-6 rounded-lg shadow hover:bg-gray-800/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-3 bg-pink-600 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-lg font-semibold text-white">Playlists</h2>
                                        <p class="text-2xl font-bold text-white">{{ stats?.playlists || 0 }}</p>
                                    </div>
                                </div>
                            </Link>

                            <Link href="/client/djs" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 p-6 rounded-lg shadow hover:bg-gray-800/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-3 bg-indigo-600 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-lg font-semibold text-white">Saved DJs</h2>
                                        <p class="text-2xl font-bold text-white">{{ stats?.savedDJs || 0 }}</p>
                                    </div>
                                </div>
                            </Link>

                            <Link href="/client/bookings?status=past" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 p-6 rounded-lg shadow hover:bg-gray-800/70 transition-colors">
                                <div class="flex items-center">
                                    <div class="p-3 bg-green-600 rounded-lg">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h2 class="text-lg font-semibold text-white">Completed Events</h2>
                                        <p class="text-2xl font-bold text-white">{{ stats?.completedEvents || 0 }}</p>
                                    </div>
                                </div>
                            </Link>
                        </div>

                        <!-- Upcoming Bookings -->
                        <div class="mb-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-white">Upcoming Bookings</h2>
                                <Link href="/client/bookings" class="text-sm text-gray-400 hover:text-white">View All</Link>
                            </div>

                            <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg shadow overflow-hidden" v-if="upcomingBookings && upcomingBookings.length > 0">
                                <ul class="divide-y divide-gray-700">
                                    <li v-for="booking in upcomingBookings" :key="booking.id" class="p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center mb-1">
                                                    <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                                    <p class="ml-2 font-medium text-white">{{ booking.event_type }}</p>
                                                </div>
                                                <p class="text-sm text-gray-400">{{ booking.event_date }} • {{ booking.start_time }} - {{ booking.end_time }}</p>
                                            </div>

                                            <div class="flex-1 text-center">
                                                <p class="text-sm font-medium text-white">{{ booking.dj_name }}</p>
                                                <p class="text-sm text-gray-400">{{ booking.venue_name }}</p>
                                            </div>

                                            <div class="flex items-center gap-3">
                                                <span class="px-2 py-1 text-xs font-medium text-green-400 bg-green-900/50 rounded-full">{{ booking.status }}</span>
                                                <Link :href="`/client/bookings/${booking.id}`" class="text-sm text-gray-400 hover:text-white">Details</Link>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div v-else class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-8 text-center">
                                <p class="text-gray-500">No upcoming bookings</p>
                            </div>
                        </div>

                        <!-- Recent Playlists & Featured DJs -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Recent Playlists -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-bold text-white">Recent Playlists</h2>
                                    <Link href="/client/playlists" class="text-sm text-gray-400 hover:text-white">View All</Link>
                                </div>

                                <div v-if="recentPlaylists && recentPlaylists.length > 0" class="grid grid-cols-1 gap-4">
                                    <Link v-for="playlist in recentPlaylists" :key="playlist.id" :href="`/client/playlists/${playlist.id}/edit`" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg shadow p-5 hover:bg-gray-800/70 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <h3 class="text-lg font-medium text-white">{{ playlist.name }}</h3>
                                            <div class="p-1 text-pink-500 bg-pink-500/10 rounded-full">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="mt-4 text-sm text-gray-400">
                                            <p>{{ playlist.song_count }} songs • {{ playlist.duration }}</p>
                                        </div>
                                    </Link>
                                </div>
                                <div v-else class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-8 text-center">
                                    <p class="text-gray-500">No playlists yet</p>
                                    <Link href="/client/playlists/create" class="inline-block mt-2 text-sm text-purple-400 hover:text-purple-300">Create your first playlist</Link>
                                </div>
                            </div>

                            <!-- Featured DJs -->
                            <div>
                                <div class="flex items-center justify-between mb-4">
                                    <h2 class="text-xl font-bold text-white">Featured DJs</h2>
                                    <Link href="/client/djs" class="text-sm text-gray-400 hover:text-white">Browse All</Link>
                                </div>

                                <div v-if="featuredDjs && featuredDjs.length > 0" class="grid grid-cols-2 gap-4">
                                    <Link v-for="dj in featuredDjs.slice(0, 2)" :key="dj.id" :href="`/client/djs/${dj.id}`" class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg shadow overflow-hidden hover:bg-gray-800/70 transition-colors">
                                        <div class="h-32 bg-gray-700 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-gray-500">DJ</span>
                                        </div>
                                        <div class="p-4">
                                            <h3 class="font-medium text-white">{{ dj.user?.name || 'DJ' }}</h3>
                                            <p class="text-sm text-purple-400">{{ dj.genres?.map(g => g.name).join(', ') || 'Various' }}</p>
                                            <div class="flex items-center mt-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                                <span class="ml-1 text-sm text-gray-300">{{ dj.rating || '5.0' }}</span>
                                            </div>
                                        </div>
                                    </Link>
                                </div>
                                <div v-else class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-lg p-8 text-center">
                                    <p class="text-gray-500">No featured DJs</p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-8">
                            <h2 class="text-xl font-bold text-white mb-4">Quick Actions</h2>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <Link href="/client/djs" class="bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white rounded-lg p-6 text-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                    <span class="font-medium">Find a DJ</span>
                                </Link>

                                <Link href="/client/playlists/create" class="bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white rounded-lg p-6 text-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                    </svg>
                                    <span class="font-medium">Create Playlist</span>
                                </Link>

                                <Link href="/client/venues/create" class="bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 text-white rounded-lg p-6 text-center transition-colors">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <span class="font-medium">Add Venue</span>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>
