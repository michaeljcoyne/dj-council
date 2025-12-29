<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import { Menu, X, CalendarDays, DollarSign, Star, Users, Music, AlertCircle } from 'lucide-vue-next';

const props = defineProps({
    profile: Object,
    upcomingBookings: Array,
    pendingBookings: Array,
    stats: Object,
});

// Sidebar state for mobile
const isMobileSidebarOpen = ref(false);
const windowWidth = ref(1024);

onMounted(() => {
    windowWidth.value = window.innerWidth;
    window.addEventListener('resize', () => {
        windowWidth.value = window.innerWidth;
    });
});

const isMobile = computed(() => {
    return windowWidth.value < 1024;
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString('en-GB', {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const formatCurrency = (amount) => {
    if (!amount) return '£0.00';
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
};

// Combine upcoming and pending for display
const allBookings = computed(() => {
    const pending = props.pendingBookings.map(b => ({ ...b, display_status: 'pending' }));
    const upcoming = props.upcomingBookings.map(b => ({ ...b, display_status: 'confirmed' }));
    return [...pending, ...upcoming].slice(0, 5);
});
</script>

<template>
    <Head title="DJ Dashboard" />

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
                    href="/dj/dashboard"
                    class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg"
                >
                    <span class="ml-3">Dashboard</span>
                </Link>
                <a
                    href="#"
                    class="flex items-center px-4 py-2 text-gray-500 cursor-not-allowed rounded-lg"
                    title="Coming soon"
                >
                    <span class="ml-3">Edit Profile</span>
                </a>
                <Link
                    href="/dj/bookings"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Bookings</span>
                </Link>
                <a
                    href="#"
                    class="flex items-center px-4 py-2 text-gray-500 cursor-not-allowed rounded-lg"
                    title="Coming soon"
                >
                    <span class="ml-3">Reviews</span>
                </a>
                <Link
                    href="/dj/playlists"
                    class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg"
                >
                    <span class="ml-3">Spotify Playlists</span>
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
                                    <div class="w-8 h-8 rounded-full bg-pink-600 flex items-center justify-center text-white font-bold">
                                        DJ
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-white">{{ profile.stage_name }}</h1>
                        <p class="mt-1 text-gray-400">{{ profile.specialty }}</p>
                    </div>
                    <Link
                        href="/dj/profile/edit"
                        class="mt-4 px-4 py-2 text-sm font-medium text-white bg-pink-600 rounded-lg hover:bg-pink-700 md:mt-0"
                    >
                        Edit Profile
                    </Link>
                </div>

                <!-- Pending Requests Alert -->
                <div v-if="pendingBookings.length > 0" class="mt-6 p-4 bg-yellow-900/20 border-2 border-yellow-500/50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <AlertCircle class="w-5 h-5 text-yellow-500" />
                        <div class="flex-1">
                            <p class="text-white font-medium">You have {{ pendingBookings.length }} pending booking request{{ pendingBookings.length > 1 ? 's' : '' }}</p>
                            <p class="text-sm text-gray-400">Review and respond to booking requests</p>
                        </div>
                        <Link
                            href="/dj/bookings?status=pending"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium text-sm"
                        >
                            View Requests
                        </Link>
                    </div>
                </div>

                <!-- Stats Overview -->
                <div class="grid grid-cols-1 gap-4 mt-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-purple-600 rounded-lg">
                                <CalendarDays class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Upcoming Bookings</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.upcoming_bookings }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-green-600 rounded-lg">
                                <DollarSign class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Total Earnings</h2>
                                <p class="text-2xl font-bold text-white">{{ formatCurrency(stats.total_earnings) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-yellow-600 rounded-lg">
                                <Star class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Rating</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.average_rating.toFixed(1) }} <span class="text-sm text-gray-400">({{ stats.review_count }})</span></p>
                            </div>
                        </div>
                    </div>

                    <div class="p-5 bg-gray-800 rounded-lg">
                        <div class="flex items-center">
                            <div class="p-3 bg-pink-600 rounded-lg">
                                <Users class="w-6 h-6 text-white" />
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold text-white">Total Events</h2>
                                <p class="text-2xl font-bold text-white">{{ stats.total_bookings }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings List -->
                <div class="mt-8">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-white">Recent Bookings</h2>
                        <Link href="/dj/bookings" class="text-sm text-purple-400 hover:text-purple-300">View All</Link>
                    </div>

                    <div class="mt-4 overflow-hidden bg-gray-800 rounded-lg shadow">
                        <ul class="divide-y divide-gray-700">
                            <li v-for="booking in allBookings" :key="booking.id" class="flex flex-col p-4 sm:flex-row sm:items-center sm:justify-between hover:bg-gray-750 transition-colors">
                                <div class="mb-2 sm:mb-0">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-shrink-0 w-2 h-2 rounded-full" :class="booking.display_status === 'confirmed' ? 'bg-green-500' : 'bg-yellow-500'"></div>
                                        <p class="font-medium text-white capitalize">{{ booking.event_type }}</p>
                                    </div>
                                    <p class="mt-1 text-sm text-gray-400">{{ formatDate(booking.event_date) }} • {{ booking.duration_hours }} hours</p>
                                </div>

                                <div class="mb-2 sm:mb-0">
                                    <p class="text-sm font-medium text-white">{{ booking.user?.name || 'Client' }}</p>
                                    <p class="text-sm text-gray-400">{{ booking.venue?.name || 'Venue TBD' }}</p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full"
                                          :class="booking.display_status === 'confirmed' ? 'text-green-400 bg-green-900' : 'text-yellow-400 bg-yellow-900'">
                                        {{ booking.display_status }}
                                    </span>
                                    <Link :href="`/dj/bookings/${booking.id}`" class="text-purple-400 hover:text-purple-300">
                                        Details
                                    </Link>
                                </div>
                            </li>

                            <li v-if="allBookings.length === 0" class="p-8 text-center text-gray-400">
                                <CalendarDays class="w-12 h-12 mx-auto mb-2 text-gray-600" />
                                <p>No bookings yet</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
