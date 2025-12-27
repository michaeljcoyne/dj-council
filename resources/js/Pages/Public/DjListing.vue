<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { Search, MapPin, Music, Star, Filter, X, Headphones } from 'lucide-vue-next';

const props = defineProps({
    djs: Object,
    genres: Array,
    filters: Object,
});

const searchQuery = ref(props.filters?.search || '');
const selectedGenre = ref(props.filters?.genre || '');
const selectedLocation = ref(props.filters?.location || '');
const minRate = ref(props.filters?.min_rate || '');
const maxRate = ref(props.filters?.max_rate || '');
const showFilters = ref(false);

const activeFiltersCount = computed(() => {
    let count = 0;
    if (selectedGenre.value) count++;
    if (selectedLocation.value) count++;
    if (minRate.value) count++;
    if (maxRate.value) count++;
    return count;
});

const applyFilters = () => {
    router.get('/djs', {
        search: searchQuery.value,
        genre: selectedGenre.value,
        location: selectedLocation.value,
        min_rate: minRate.value,
        max_rate: maxRate.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedGenre.value = '';
    selectedLocation.value = '';
    minRate.value = '';
    maxRate.value = '';
    applyFilters();
};

// Debounced search
let searchTimeout;
watch(searchQuery, () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 500);
});

const formatRate = (rate) => {
    return new Intl.NumberFormat('en-GB', {
        style: 'currency',
        currency: 'GBP',
        maximumFractionDigits: 0
    }).format(rate);
};
</script>

<template>
    <Head title="Find DJs" />

    <div class="min-h-screen bg-black text-white">
        <!-- Header - EXACT match to Welcome page -->
        <header class="fixed top-0 left-0 right-0 z-50 bg-black/80 backdrop-blur-md border-b border-purple-500/20">
            <div class="container mx-auto px-4 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <Headphones class="h-6 w-6 text-white" />
                    </div>
                    <span class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-500 to-purple-500">
                        DJ Council
                    </span>
                </Link>

                <nav class="hidden md:flex items-center space-x-6">
                    <Link href="/" class="text-white hover:text-pink-400 transition-colors">
                        Home
                    </Link>
                    <Link href="/djs" class="text-pink-400">
                        Find DJs
                    </Link>

                    <div class="ml-4">
                        <Link href="/login" class="mr-2 text-white hover:text-pink-400 transition-colors">
                            Log in
                        </Link>
                        <Link href="/register" class="px-4 py-2 rounded-lg bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-medium">
                            Sign Up
                        </Link>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content - NO GAP, starts right after header -->
        <main class="pt-16">
            <section class="min-h-screen py-8 bg-gradient-to-b from-purple-950/50 to-black">
                <div class="container mx-auto px-4">
                    <!-- Hero Search -->
                    <div class="mb-6">
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Find Your Perfect DJ</h1>
                        <p class="text-gray-400 mb-4">Browse {{ djs.total }} talented DJs ready to make your event unforgettable</p>

                        <!-- Search Bar -->
                        <div class="flex gap-3 mb-4">
                            <div class="flex-1 relative">
                                <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-500" />
                                <input
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search DJs by name, specialty, or location..."
                                    class="w-full h-12 pl-10 pr-4 bg-purple-950/30 border border-purple-500/30 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                />
                            </div>
                            <button
                                @click="showFilters = !showFilters"
                                class="px-6 h-12 bg-purple-950/30 border border-purple-500/30 hover:bg-purple-900/50 text-white rounded-lg transition-colors flex items-center gap-2"
                            >
                                <Filter class="w-5 h-5" />
                                Filters
                                <span v-if="activeFiltersCount > 0" class="ml-1 px-2 py-0.5 bg-pink-600 text-white text-xs font-medium rounded-full">
                            {{ activeFiltersCount }}
                        </span>
                            </button>
                        </div>

                        <!-- Filter Panel -->
                        <Transition
                            enter-active-class="transition duration-200 ease-out"
                            enter-from-class="opacity-0 -translate-y-2"
                            enter-to-class="opacity-100 translate-y-0"
                            leave-active-class="transition duration-150 ease-in"
                            leave-from-class="opacity-100 translate-y-0"
                            leave-to-class="opacity-0 -translate-y-2"
                        >
                            <div v-if="showFilters" class="bg-black/50 backdrop-blur-sm border border-purple-500/30 rounded-xl p-6">
                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <!-- Genre -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 uppercase mb-2">Genre</label>
                                        <select
                                            v-model="selectedGenre"
                                            @change="applyFilters"
                                            class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white text-sm focus:outline-none focus:ring-2 focus:ring-pink-500 appearance-none cursor-pointer"
                                            style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 width=%2724%27 height=%2724%27 viewBox=%270 0 24 24%27 fill=%27none%27 stroke=%27%23a855f7%27 stroke-width=%272%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27%3E%3Cpath d=%27m6 9 6 6 6-6%27/%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 0.5rem center; background-size: 1.5em 1.5em; padding-right: 2.5rem;"
                                        >
                                            <option value="">All Genres</option>
                                            <option v-for="genre in genres" :key="genre.id" :value="genre.id">
                                                {{ genre.name }}
                                            </option>
                                        </select>
                                    </div>

                                    <!-- Location -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 uppercase mb-2">Location</label>
                                        <input
                                            v-model="selectedLocation"
                                            @input="applyFilters"
                                            type="text"
                                            placeholder="e.g., London"
                                            class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500"
                                        />
                                    </div>

                                    <!-- Min Rate -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 uppercase mb-2">Min Rate (£/hr)</label>
                                        <input
                                            v-model="minRate"
                                            @input="applyFilters"
                                            type="number"
                                            placeholder="0"
                                            min="0"
                                            class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                        />
                                    </div>

                                    <!-- Max Rate -->
                                    <div>
                                        <label class="block text-xs font-medium text-gray-400 uppercase mb-2">Max Rate (£/hr)</label>
                                        <input
                                            v-model="maxRate"
                                            @input="applyFilters"
                                            type="number"
                                            placeholder="500"
                                            min="0"
                                            class="w-full px-3 py-2 bg-purple-950/50 border border-purple-500/30 rounded-lg text-white text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-pink-500 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                        />
                                    </div>
                                </div>

                                <div class="mt-4 flex justify-end">
                                    <button
                                        @click="clearFilters"
                                        class="text-sm text-gray-400 hover:text-white transition-colors flex items-center gap-1"
                                    >
                                        <X class="w-4 h-4" />
                                        Clear Filters
                                    </button>
                                </div>
                            </div>
                        </Transition>
                    </div>

                    <!-- Results -->
                    <div v-if="djs.data.length === 0" class="text-center py-16">
                        <Music class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                        <h3 class="text-xl font-semibold text-white mb-2">No DJs found</h3>
                        <p class="text-gray-400 mb-4">Try adjusting your filters</p>
                        <button @click="clearFilters" class="text-pink-500 hover:text-pink-400 text-sm font-medium">
                            Clear all filters
                        </button>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <Link
                            v-for="dj in djs.data"
                            :key="dj.id"
                            :href="`/djs/${dj.id}`"
                            class="group bg-purple-950/30 backdrop-blur-sm rounded-xl border-2 border-purple-500/30 overflow-hidden hover:border-pink-500/50 transition-all"
                        >
                            <!-- DJ Image -->
                            <div class="relative overflow-hidden aspect-[4/5]">
                                <img
                                    v-if="dj.profile_image"
                                    :src="dj.profile_image"
                                    :alt="dj.stage_name"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div v-else class="flex items-center justify-center h-full bg-gradient-to-br from-pink-500/20 to-purple-600/20">
                                    <Music class="w-12 h-12 text-gray-600" />
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent"></div>
                                <div v-if="dj.is_featured" class="absolute top-3 right-3 px-2 py-1 bg-gradient-to-r from-pink-600 to-purple-600 text-white text-xs font-medium rounded-full">
                                    Featured
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-4">
                                    <div class="flex items-center mb-1">
                                        <Star class="w-4 h-4 text-yellow-500 fill-yellow-500" />
                                        <span class="ml-1 font-medium text-white text-sm">{{ dj.average_rating?.toFixed(1) || 'New' }}</span>
                                        <span v-if="dj.review_count" class="ml-1 text-gray-400 text-xs">({{ dj.review_count }})</span>
                                    </div>
                                    <h3 class="text-xl font-bold text-white mb-1 group-hover:text-pink-400 transition-colors">
                                        {{ dj.stage_name }}
                                    </h3>
                                    <p class="text-pink-400 text-sm">{{ dj.specialty }}</p>
                                </div>
                            </div>

                            <!-- DJ Info - Connected to image -->
                            <div class="p-4">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <div v-if="dj.location" class="flex items-center gap-1 text-gray-400 text-xs mb-1">
                                            <MapPin class="w-3 h-3" />
                                            {{ dj.location }}
                                        </div>
                                        <div class="text-base font-bold text-white">{{ formatRate(dj.hourly_rate) }}/hr</div>
                                        <div class="text-xs text-gray-500">{{ dj.minimum_booking_hours }}hr min</div>
                                    </div>
                                </div>

                                <!-- Genres -->
                                <div class="flex flex-wrap gap-1 mb-3">
                            <span
                                v-for="genre in dj.genres?.slice(0, 3)"
                                :key="genre.id"
                                class="px-2 py-1 bg-purple-900/50 rounded-full text-xs text-gray-300"
                            >
                                {{ genre.name }}
                            </span>
                                    <span v-if="dj.genres?.length > 3" class="px-2 py-0.5 text-gray-500 text-xs">
                                +{{ dj.genres.length - 3 }}
                            </span>
                                </div>

                                <div class="flex gap-2">
                            <span class="flex-1 px-3 py-2 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 rounded-lg font-medium text-white text-sm text-center">
                                Book Now
                            </span>
                                    <span class="px-3 py-2 border border-pink-500 text-pink-500 rounded-lg hover:bg-pink-950 transition-colors text-sm">
                                View
                            </span>
                                </div>
                            </div>
                        </Link>
                    </div>

                    <!-- Pagination -->
                    <div v-if="djs.last_page > 1" class="mt-8 flex justify-center gap-2">
                        <Link
                            v-if="djs.prev_page_url"
                            :href="djs.prev_page_url"
                            preserve-state
                            class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm transition-colors"
                        >
                            Previous
                        </Link>
                        <span class="px-4 py-2 text-gray-400 text-sm">
                    Page {{ djs.current_page }} of {{ djs.last_page }}
                </span>
                        <Link
                            v-if="djs.next_page_url"
                            :href="djs.next_page_url"
                            preserve-state
                            class="px-4 py-2 bg-purple-900/50 hover:bg-purple-900 text-white rounded-lg text-sm transition-colors"
                        >
                            Next
                        </Link>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-black border-t border-purple-500/30 py-8">
            <div class="container mx-auto px-4 text-center text-gray-500 text-sm">
                <p>&copy; {{ new Date().getFullYear() }} DJ Council. All rights reserved.</p>
            </div>
        </footer>
    </div>
</template>
