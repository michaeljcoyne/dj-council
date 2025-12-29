<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { Menu, X, Calendar, Clock, MapPin, User, ChevronRight, CheckCircle, XCircle, AlertCircle } from 'lucide-vue-next';
import { ref, onMounted, computed } from 'vue';

const props = defineProps({
    bookings: Object,
    activeStatus: String,
});

// Sidebar state
const isMobileSidebarOpen = ref(false);
const windowWidth = ref(1024);

onMounted(() => {
    windowWidth.value = window.innerWidth;
    window.addEventListener('resize', () => {
        windowWidth.value = window.innerWidth;
    });
});

const isMobile = computed(() => windowWidth.value < 1024);

const statusTabs = [
    { value: 'pending', label: 'Pending', icon: AlertCircle, color: 'yellow' },
    { value: 'upcoming', label: 'Upcoming', icon: Calendar, color: 'green' },
    { value: 'past', label: 'Past', icon: CheckCircle, color: 'blue' },
    { value: 'cancelled', label: 'Cancelled', icon: XCircle, color: 'red' },
];

const getStatusColor = (status) => {
    const colors = {
        pending: 'text-yellow-400 bg-yellow-400/10 border-yellow-400/30',
        confirmed: 'text-green-400 bg-green-400/10 border-green-400/30',
        completed: 'text-blue-400 bg-blue-400/10 border-blue-400/30',
        cancelled: 'text-red-400 bg-red-400/10 border-red-400/30',
        declined: 'text-red-400 bg-red-400/10 border-red-400/30',
    };
    return colors[status] || 'text-gray-400 bg-gray-400/10 border-gray-400/30';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-GB', {
        weekday: 'short',
        day: 'numeric',
        month: 'short',
        year: 'numeric'
    });
};

const formatCurrency = (amount) => {
    if (!amount) return 'TBD';
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
};
</script>

<template>
    <Head title="My Bookings - DJ Dashboard" />

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
                <Link href="/dj/dashboard" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <span class="ml-3">Dashboard</span>
                </Link>
                <a href="#" class="flex items-center px-4 py-2 text-gray-500 cursor-not-allowed rounded-lg" title="Coming soon">
                    <span class="ml-3">Edit Profile</span>
                </a>
                <Link href="/dj/bookings" class="flex items-center px-4 py-2 text-white bg-purple-700 rounded-lg">
                    <span class="ml-3">Bookings</span>
                </Link>
                <a href="#" class="flex items-center px-4 py-2 text-gray-500 cursor-not-allowed rounded-lg" title="Coming soon">
                    <span class="ml-3">Reviews</span>
                </a>
                <Link href="/dj/playlists" class="flex items-center px-4 py-2 text-gray-300 hover:bg-gray-700 rounded-lg">
                    <span class="ml-3">Spotify Playlists</span>
                </Link>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="lg:pl-64">
            <!-- Top Navigation -->
            <div class="bg-gray-800 shadow">
                <div class="flex items-center justify-between px-4 py-3">
                    <h1 class="text-2xl font-bold text-white lg:ml-0 ml-12">My Bookings</h1>
                </div>
            </div>

            <!-- Dashboard Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <!-- Status Tabs -->
                <div class="mb-6 flex gap-2 overflow-x-auto pb-2">
                    <Link
                        v-for="tab in statusTabs"
                        :key="tab.value"
                        :href="`/dj/bookings?status=${tab.value}`"
                        :class="[
                            'flex items-center gap-2 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap',
                            activeStatus === tab.value
                                ? 'bg-gradient-to-r from-pink-600 to-purple-600 text-white'
                                : 'bg-gray-800 border border-gray-700 text-gray-400 hover:text-white hover:border-gray-600'
                        ]"
                    >
                        <component :is="tab.icon" class="w-4 h-4" />
                        {{ tab.label }}
                    </Link>
                </div>

                <!-- Bookings List -->
                <div v-if="bookings.data.length > 0" class="space-y-4">
                    <Link
                        v-for="booking in bookings.data"
                        :key="booking.id"
                        :href="`/dj/bookings/${booking.id}`"
                        class="block bg-gray-800 border-2 border-gray-700 rounded-xl p-6 hover:border-purple-500 transition-all group"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <!-- Event Type & Status -->
                                <div class="flex items-center gap-3 mb-3">
                                    <span class="text-lg font-bold text-white capitalize">
                                        {{ booking.event_type }}
                                    </span>
                                    <span :class="['px-3 py-1 rounded-full text-xs font-medium capitalize border', getStatusColor(booking.status)]">
                                        {{ booking.status === 'cancelled' ? 'Declined' : booking.status }}
                                    </span>
                                </div>

                                <!-- Event Date & Time -->
                                <div class="flex items-center gap-4 text-sm text-gray-400 mb-3">
                                    <div class="flex items-center gap-2">
                                        <Calendar class="w-4 h-4 text-pink-500" />
                                        <span>{{ formatDate(booking.event_date) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <Clock class="w-4 h-4 text-pink-500" />
                                        <span>{{ booking.duration_hours }} hours</span>
                                    </div>
                                </div>

                                <!-- Client & Venue Info -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                                    <div class="flex items-center gap-2">
                                        <User class="w-4 h-4 text-purple-400" />
                                        <span class="text-gray-300">{{ booking.user?.name || 'Client' }}</span>
                                    </div>
                                    <div v-if="booking.venue" class="flex items-center gap-2">
                                        <MapPin class="w-4 h-4 text-purple-400" />
                                        <span class="text-gray-300">{{ booking.venue?.name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Price & Arrow -->
                            <div class="flex flex-col items-end gap-2">
                                <div class="text-right">
                                    <div class="text-xl font-bold text-pink-500">
                                        {{ formatCurrency(booking.total_price) }}
                                    </div>
                                </div>
                                <ChevronRight class="w-5 h-5 text-gray-500 group-hover:text-pink-500 transition-colors" />
                            </div>
                        </div>
                    </Link>
                </div>

                <!-- Empty State -->
                <div v-else class="bg-gray-800 border-2 border-gray-700 rounded-xl p-12 text-center">
                    <Calendar class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                    <h3 class="text-xl font-bold text-white mb-2">No bookings found</h3>
                    <p class="text-gray-400">
                        {{ activeStatus === 'pending' ? "No pending booking requests." : `No ${activeStatus} bookings.` }}
                    </p>
                </div>

                <!-- Pagination -->
                <div v-if="bookings.last_page > 1" class="mt-6 flex justify-center gap-2">
                    <Link
                        v-if="bookings.prev_page_url"
                        :href="bookings.prev_page_url"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm"
                    >
                        Previous
                    </Link>
                    <span class="px-4 py-2 text-gray-400 text-sm">
                        Page {{ bookings.current_page }} of {{ bookings.last_page }}
                    </span>
                    <Link
                        v-if="bookings.next_page_url"
                        :href="bookings.next_page_url"
                        class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white rounded-lg text-sm"
                    >
                        Next
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
