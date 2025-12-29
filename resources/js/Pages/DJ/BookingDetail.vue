<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import {
    Menu, X, Calendar, Clock, MapPin, Music, User, FileText,
    CheckCircle, XCircle, MessageSquare, Phone, Mail
} from 'lucide-vue-next';

const props = defineProps({
    booking: Object,
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
        weekday: 'long',
        day: 'numeric',
        month: 'long',
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
    if (!amount) return 'TBD';
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP'
    }).format(amount);
};

const acceptBooking = () => {
    if (!confirm('Accept this booking request?')) return;

    router.post(`/dj/bookings/${props.booking.id}/accept`, {}, {
        preserveScroll: true,
    });
};

const declineBooking = () => {
    const reason = prompt('Reason for declining (optional):');
    if (reason === null) return; // User cancelled

    router.post(`/dj/bookings/${props.booking.id}/decline`, {
        reason: reason || 'No reason provided'
    }, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="`Booking #${booking.id} - DJ Dashboard`" />

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
                <div class="px-4 py-3">
                    <Link href="/dj/bookings" class="text-gray-400 hover:text-pink-500 transition-colors text-sm lg:ml-0 ml-12">
                        ← Back to Bookings
                    </Link>
                </div>
            </div>

            <!-- Content -->
            <div class="px-4 py-6 sm:px-6 lg:px-8">
                <!-- Page Header -->
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Booking #{{ booking.id }}
                        </h1>
                        <p class="text-gray-400 capitalize">{{ booking.event_type }} Event</p>
                    </div>
                    <div :class="['px-4 py-2 rounded-lg border-2 font-medium capitalize', getStatusColor(booking.status)]">
                        {{ booking.status }}
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Details -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Event Details -->
                        <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <Calendar class="w-5 h-5 text-pink-500" />
                                Event Details
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <label class="block text-gray-400 mb-1">Date</label>
                                    <div class="text-white font-medium">{{ formatDate(booking.event_date) }}</div>
                                </div>
                                <div>
                                    <label class="block text-gray-400 mb-1">Time</label>
                                    <div class="text-white font-medium">{{ formatTime(booking.event_date) }}</div>
                                </div>
                                <div>
                                    <label class="block text-gray-400 mb-1">Duration</label>
                                    <div class="text-white font-medium">{{ booking.duration_hours }} hours</div>
                                </div>
                                <div>
                                    <label class="block text-gray-400 mb-1">Event Type</label>
                                    <div class="text-white font-medium capitalize">{{ booking.event_type }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Client Details -->
                        <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <User class="w-5 h-5 text-pink-500" />
                                Client
                            </h2>

                            <div class="space-y-3">
                                <div>
                                    <div class="font-medium text-white text-lg">{{ booking.user?.name || 'Client' }}</div>
                                    <div class="text-sm text-gray-400">{{ booking.user?.email || 'No email' }}</div>
                                </div>

                                <div class="flex gap-2 mt-4">
                                    <button class="flex-1 px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm transition-colors">
                                        <MessageSquare class="w-4 h-4 inline mr-1" />
                                        Message
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Venue Details -->
                        <div v-if="booking.venue" class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <MapPin class="w-5 h-5 text-pink-500" />
                                Venue
                            </h2>

                            <div class="space-y-1">
                                <div class="text-white font-medium text-lg">{{ booking.venue.name }}</div>
                                <div class="text-gray-400">{{ booking.venue.address }}</div>
                                <div class="text-gray-400">{{ booking.venue.city }}, {{ booking.venue.state }} {{ booking.venue.postal_code }}</div>
                            </div>
                        </div>

                        <!-- Playlist -->
                        <div v-if="booking.playlist" class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <Music class="w-5 h-5 text-pink-500" />
                                Client Playlist
                            </h2>

                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-medium text-white">{{ booking.playlist.name }}</div>
                                    <div class="text-sm text-gray-400">Shared for this event</div>
                                </div>
                                <Link
                                    :href="`/dj/playlists/${booking.playlist.id}`"
                                    class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm transition-colors"
                                >
                                    View
                                </Link>
                            </div>
                        </div>

                        <!-- Special Requests -->
                        <div v-if="booking.special_requests" class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-white mb-4 flex items-center gap-2">
                                <FileText class="w-5 h-5 text-pink-500" />
                                Special Requests
                            </h2>

                            <p class="text-gray-300 whitespace-pre-wrap">{{ booking.special_requests }}</p>
                        </div>

                        <!-- Decline Reason (if declined) -->
                        <div v-if="booking.status === 'declined' && booking.decline_reason" class="bg-red-900/20 border-2 border-red-500/50 rounded-xl p-6">
                            <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                                <XCircle class="w-5 h-5" />
                                Decline Reason
                            </h2>

                            <p class="text-gray-300 whitespace-pre-wrap">{{ booking.decline_reason }}</p>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-24 space-y-6">
                            <!-- Price Summary -->
                            <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-white mb-4">Payment</h3>

                                <div class="space-y-3 text-sm mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Duration:</span>
                                        <span class="text-white">{{ booking.duration_hours }} hours</span>
                                    </div>
                                    <div class="border-t border-gray-700 pt-3">
                                        <div class="flex justify-between text-lg">
                                            <span class="text-white font-bold">Total:</span>
                                            <span class="text-pink-500 font-bold">{{ formatCurrency(booking.total_price) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="booking.status === 'pending'" class="text-xs text-gray-500 text-center">
                                    Payment will be processed after you accept
                                </div>
                                <div v-else-if="booking.status === 'confirmed'" class="text-xs text-green-400 text-center">
                                    Payment confirmed
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-white mb-4">Actions</h3>

                                <div class="space-y-2">
                                    <!-- Accept Button (pending only) -->
                                    <button
                                        v-if="booking.status === 'pending'"
                                        @click="acceptBooking"
                                        class="w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 rounded-lg font-medium text-white transition-all"
                                    >
                                        <CheckCircle class="w-4 h-4 inline mr-2" />
                                        Accept Booking
                                    </button>

                                    <!-- Decline Button (pending only) -->
                                    <button
                                        v-if="booking.status === 'pending'"
                                        @click="declineBooking"
                                        class="w-full px-4 py-3 border-2 border-red-500 text-red-500 hover:bg-red-950 rounded-lg font-medium transition-colors"
                                    >
                                        <XCircle class="w-4 h-4 inline mr-2" />
                                        Decline Booking
                                    </button>

                                    <!-- Message Client -->
                                    <button
                                        v-if="booking.status === 'confirmed'"
                                        class="w-full px-4 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-white transition-all"
                                    >
                                        <MessageSquare class="w-4 h-4 inline mr-2" />
                                        Message Client
                                    </button>

                                    <Link
                                        href="/dj/bookings"
                                        class="block w-full px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium text-center transition-colors"
                                    >
                                        Back to Bookings
                                    </Link>
                                </div>
                            </div>

                            <!-- Status Info -->
                            <div class="bg-gray-800 border-2 border-gray-700 rounded-xl p-6">
                                <h3 class="text-lg font-bold text-white mb-4">Booking Status</h3>

                                <div class="space-y-3 text-sm">
                                    <div class="flex items-start gap-3">
                                        <div :class="['w-2 h-2 rounded-full mt-1.5', booking.status ? 'bg-green-400' : 'bg-gray-600']"></div>
                                        <div>
                                            <div class="text-white font-medium">Request Received</div>
                                            <div class="text-gray-400 text-xs">{{ formatDate(booking.created_at) }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div :class="['w-2 h-2 rounded-full mt-1.5', booking.status === 'confirmed' || booking.status === 'completed' ? 'bg-green-400' : 'bg-gray-600']"></div>
                                        <div>
                                            <div class="text-white font-medium">
                                                {{ booking.status === 'confirmed' || booking.status === 'completed' ? 'Accepted' : 'Awaiting Response' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <div :class="['w-2 h-2 rounded-full mt-1.5', booking.status === 'completed' ? 'bg-green-400' : 'bg-gray-600']"></div>
                                        <div>
                                            <div class="text-white font-medium">Event Complete</div>
                                            <div class="text-gray-400 text-xs">{{ formatDate(booking.event_date) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
