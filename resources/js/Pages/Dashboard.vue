<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { Menu, X, Calendar, Music, Bookmark } from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';

// Sidebar state for mobile
const isMobileSidebarOpen = ref(false);
const windowWidth = ref(1024); // Default value for server-side rendering

// Check if we're in the browser environment
onMounted(() => {
    windowWidth.value = window.innerWidth;
    window.addEventListener('resize', () => {
        windowWidth.value = window.innerWidth;
    });
});

// Compute whether the sidebar should be shown
const isMobile = computed(() => {
    return windowWidth.value < 1024;
});

// Fake data for demonstration
const upcomingBookings = [
    {
        id: 1,
        date: 'Jun.15, 2025',
        time: '8:00 PM - 12:00 AM',
        dj: 'DJ Pulse',
        venue: 'Skyline Rooftop',
        type: 'Corporate Event',
        status: 'confirmed'
    },
    {
        id: 2,
        date: 'Jul.10, 2025',
        time: '9:00 PM - 2:00 AM',
        dj: 'DJ Vinyl Queen',
        venue: 'The Grand Hall',
        type: 'Wedding Reception',
        status: 'confirmed'
    }
];

const recentPlaylists = [
    {
        id: 1,
        name: 'Summer Party Mix',
        songs: 15,
        duration: '52 min'
    },
    {
        id: 2,
        name: 'Corporate Event',
        songs: 22,
        duration: '1h 15min'
    },
    {
        id: 3,
        name: 'Wedding Dance Floor',
        songs: 18,
        duration: '1h 05min'
    }
];

const featuredDjs = [
    {
        id: 1,
        name: 'DJ Pulse',
        specialty: 'House & EDM',
        image: '/placeholder.svg?height=150&width=150',
        rating: 4.9
    },
    {
        id: 2,
        name: 'DJ Vinyl Queen',
        specialty: 'Disco & Funk',
        image: '/placeholder.svg?height=150&width=150',
        rating: 4.8
    },
    {
        id: 3,
        name: 'DJ Rhythm',
        specialty: 'Hip-Hop & R&B',
        image: '/placeholder.svg?height=150&width=150',
        rating: 4.7
    },
    {
        id: 4,
        name: 'DJ Electro',
        specialty: 'Techno & EDM',
        image: '/placeholder.svg?height=150&width=150',
        rating: 4.6
    }
];
</script>

<template>
    <Head title="Client Dashboard" />

    <div class="min-h-screen bg-gray-900">
        <!-- Mobile Sidebar Toggle -->
        <div class="fixed top-0 left-0 z-50 p-4 lg:hidden">
            <button @click="isMobileSidebarOpen = !isMobileSidebarOpen" class="p-2 text-white bg-purple-800 rounded-lg">
                <Menu v-if="!isMobileSidebarOpen" class="w-6 h-6" />
                <X v-else class="w-6 h-6" />
            </button>
        </div>

        <!-- Sidebar -->
        <aside
            class="fixed inset-y-0 left-0 z-40 w-64 transition-transform duration-300 transform bg-gray-800 lg:transform-none"
            :class="{ '-translate-x-full': !isMobileSidebarOpen && isMobile }"
        >
            <div class="flex items-center justify-center h-16 px-4 bg-gray-900">
                <div class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">DJ</span>
                    </div>
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
            DJ Council
          </span>
                </div>
            </div>

            <nav class="px-4 py-6 space-y-1">
                <Link
                    href="/client/dashboard"
                    class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg"
                >
                    <span class="ml-3">Dashboard</span>
                </Link>
                <Link
                    href="/client/djs"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Find DJs</span>
                </Link>
                <Link
                    href="/client/bookings"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">My Bookings</span>
                </Link>
                <Link
                    href="/client/playlists"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">My Playlists</span>
                </Link>
                <Link
                    href="/client/venues"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">My Venues</span>
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="lg:pl-64">
            <!-- Top Navigation -->
            <div class="bg-gray-800 shadow">
                <div class="flex items-center justify-end px-4 py-3">
                    <div class="flex items-center space-x-3">
                        <button class="p-1 text-gray-400 rounded-full hover:bg-gray-700">
                            <span class="sr-only">Notifications</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </button>
                        <div class="relative ml-3">
                            <div>
                                <button class="flex text-sm bg-gray-800 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-white">
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

            <!-- Dashboard Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-white">Dashboard</h1>
                <p class="mt-2 text-gray-400">Welcome back! Here's an overview of your upcoming events and playlists.</p>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-600 rounded-lg">
                                <Calendar class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Upcoming Events</h2>
                                <p class="text-2xl font-bold text-white">2</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-pink-600 rounded-lg">
                                <Music class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Playlists</h2>
                                <p class="text-2xl font-bold text-white">3</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-indigo-600 rounded-lg">
                                <Bookmark class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Saved DJs</h2>
                                <p class="text-2xl font-bold text-white">5</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-600 rounded-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Upcoming Bookings</h2>
                        <Link href="/client/bookings" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                    </div>

                    <div class="mt-4 overflow-hidden bg-gray-800 rounded-lg shadow">
                        <ul class="divide-y divide-gray-700">
                            <li v-for="booking in upcomingBookings" :key="booking.id" class="flex flex-col p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="mb-2 sm:mb-0">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full"></div>
                                        <p class="ml-2 font-medium text-white">{{ booking.type }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-400">{{ booking.date }} • {{ booking.time }}</p>
                                </div>

                                <div class="mb-2 sm:mb-0">
                                    <p class="text-sm font-medium text-white">{{ booking.dj }}</p>
                                    <p class="text-sm text-gray-400">{{ booking.venue }}</p>
                                </div>

                                <div class="flex items-center">
                  <span class="px-2 py-1 text-xs font-medium text-green-400 bg-green-900 rounded-full">
                    {{ booking.status }}
                  </span>
                                    <Link :href="`/client/bookings/${booking.id}`" class="ml-4 text-purple-400 hover:text-purple-300">
                                        Details
                                    </Link>
                                </div>
                            </li>

                            <li v-if="upcomingBookings.length === 0" class="p-4 text-center text-gray-400">
                                No upcoming bookings
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Recent Playlists -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Recent Playlists</h2>
                        <Link href="/client/playlists" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div v-for="playlist in recentPlaylists" :key="playlist.id" class="overflow-hidden bg-gray-800 rounded-lg shadow">
                            <div class="p-5">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium text-white">{{ playlist.name }}</h3>
                                    <div class="p-1 text-pink-500 bg-pink-500/10 rounded-full">
                                        <Music class="w-5 h-5" />
                                    </div>
                                </div>
                                <div class="mt-4 text-sm text-gray-400">
                                    <p>{{ playlist.songs }} songs • {{ playlist.duration }}</p>
                                </div>
                                <div class="mt-4">
                                    <Link :href="`/client/playlists/${playlist.id}`" class="text-sm text-purple-400 hover:text-purple-300">
                                        View Playlist
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Featured DJs -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Featured DJs</h2>
                        <Link href="/client/djs" class="text-sm text-purple-400 hover:text-purple-300">Browse All</Link>
                    </div>

                    <div class="grid grid-cols-1 gap-4 mt-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div v-for="dj in featuredDjs" :key="dj.id" class="overflow-hidden bg-gray-800 rounded-lg shadow">
                            <img :src="dj.image" :alt="dj.name" class="object-cover w-full h-48" />
                            <div class="p-4">
                                <h3 class="font-medium text-white">{{ dj.name }}</h3>
                                <p class="text-sm text-purple-400">{{ dj.specialty }}</p>
                                <div class="flex items-center mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="ml-1 text-sm text-gray-300">{{ dj.rating }}</span>
                                </div>
                                <div class="mt-4">
                                    <Link :href="`/client/djs/${dj.id}`" class="text-sm text-purple-400 hover:text-purple-300">
                                        View Profile
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
