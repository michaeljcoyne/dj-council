<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Music } from 'lucide-vue-next';

const props = defineProps({
    genres: Array,
});

const form = useForm({
    stage_name: '',
    specialty: '',
    bio: '',
    hourly_rate: 100,
    minimum_booking_hours: 2,
    location: '',
    genre_ids: [],
});

const submit = () => {
    form.post(route('dj.profile.store'));
};
</script>

<template>
    <Head title="Create DJ Profile" />

    <div class="min-h-screen bg-gray-900 py-12">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-purple-600 rounded-lg flex items-center justify-center">
                        <Music class="w-10 h-10 text-white" />
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white">Create Your DJ Profile</h1>
                <p class="mt-2 text-lg text-gray-400">
                    Set up your professional DJ profile to start getting bookings.
                </p>
            </div>

            <div class="bg-gray-800 rounded-lg shadow overflow-hidden">
                <form @submit.prevent="submit" class="p-8">
                    <div class="space-y-6">
                        <!-- Basic Info -->
                        <div>
                            <h2 class="text-xl font-medium text-white mb-4">Basic Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="stage_name" class="block text-sm font-medium text-gray-300 mb-1">
                                        Stage Name *
                                    </label>
                                    <input
                                        id="stage_name"
                                        v-model="form.stage_name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                        placeholder="DJ Awesome"
                                    />
                                    <span v-if="form.errors.stage_name" class="text-red-400 text-sm mt-1">{{ form.errors.stage_name }}</span>
                                </div>

                                <div>
                                    <label for="specialty" class="block text-sm font-medium text-gray-300 mb-1">
                                        Specialty *
                                    </label>
                                    <input
                                        id="specialty"
                                        v-model="form.specialty"
                                        type="text"
                                        required
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                        placeholder="House & EDM Specialist"
                                    />
                                    <span v-if="form.errors.specialty" class="text-red-400 text-sm mt-1">{{ form.errors.specialty }}</span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label for="location" class="block text-sm font-medium text-gray-300 mb-1">
                                    Location *
                                </label>
                                <input
                                    id="location"
                                    v-model="form.location"
                                    type="text"
                                    required
                                    class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                    placeholder="London, UK"
                                />
                                <span v-if="form.errors.location" class="text-red-400 text-sm mt-1">{{ form.errors.location }}</span>
                            </div>

                            <div class="mt-6">
                                <label for="bio" class="block text-sm font-medium text-gray-300 mb-1">
                                    Bio *
                                </label>
                                <textarea
                                    id="bio"
                                    v-model="form.bio"
                                    rows="4"
                                    required
                                    class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500 resize-none"
                                    placeholder="Tell potential clients about yourself, your style, and your experience..."
                                ></textarea>
                                <span v-if="form.errors.bio" class="text-red-400 text-sm mt-1">{{ form.errors.bio }}</span>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div>
                            <h2 class="text-xl font-medium text-white mb-4">Pricing</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="hourly_rate" class="block text-sm font-medium text-gray-300 mb-1">
                                        Hourly Rate (£) *
                                    </label>
                                    <input
                                        id="hourly_rate"
                                        v-model="form.hourly_rate"
                                        type="number"
                                        min="1"
                                        required
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                    />
                                    <span v-if="form.errors.hourly_rate" class="text-red-400 text-sm mt-1">{{ form.errors.hourly_rate }}</span>
                                </div>

                                <div>
                                    <label for="minimum_booking_hours" class="block text-sm font-medium text-gray-300 mb-1">
                                        Minimum Hours *
                                    </label>
                                    <input
                                        id="minimum_booking_hours"
                                        v-model="form.minimum_booking_hours"
                                        type="number"
                                        min="1"
                                        required
                                        class="w-full px-4 py-2 rounded-lg bg-gray-700 border border-gray-600 text-white focus:outline-none focus:border-purple-500"
                                    />
                                    <span v-if="form.errors.minimum_booking_hours" class="text-red-400 text-sm mt-1">{{ form.errors.minimum_booking_hours }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Genres -->
                        <div v-if="genres && genres.length">
                            <h2 class="text-xl font-medium text-white mb-4">Genres</h2>
                            <p class="text-sm text-gray-400 mb-3">Select the genres you specialize in</p>

                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                                <label
                                    v-for="genre in genres"
                                    :key="genre.id"
                                    class="flex items-center gap-2 p-3 bg-gray-700 border border-gray-600 rounded-lg cursor-pointer hover:border-purple-500 transition-colors"
                                >
                                    <input
                                        type="checkbox"
                                        :value="genre.id"
                                        v-model="form.genre_ids"
                                        class="rounded text-pink-600 focus:ring-pink-500 focus:ring-offset-gray-900"
                                    />
                                    <span class="text-sm text-white">{{ genre.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                            <p class="text-sm text-gray-400">
                                Fields marked with * are required
                            </p>
                            <div class="flex gap-3">
                                <Link
                                    href="/client/dashboard"
                                    class="px-6 py-3 bg-gray-700 hover:bg-gray-600 rounded-lg text-white font-medium"
                                >
                                    Cancel
                                </Link>
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-3 bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg text-white font-medium"
                                >
                                    {{ form.processing ? 'Creating...' : 'Create DJ Profile' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
