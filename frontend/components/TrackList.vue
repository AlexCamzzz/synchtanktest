<template>
  <div class="max-w-6xl mx-auto">
    <!-- Stats Bar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <div class="w-12 h-12 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-lg flex items-center justify-center">
            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
            </svg>
          </div>
          <div>
            <h2 class="text-2xl font-bold text-gray-800">Your Tracks</h2>
            <p class="text-sm text-gray-600">{{ tracks.length }} {{ tracks.length === 1 ? 'track' : 'tracks' }} in your collection</p>
          </div>
        </div>

        <div v-if="tracks.length > 0" class="hidden sm:flex items-center space-x-6">
          <div class="text-center">
            <p class="text-2xl font-bold text-indigo-600">{{ totalDuration }}</p>
            <p class="text-xs text-gray-600 uppercase tracking-wide">Total Time</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-16">
      <div class="inline-flex items-center space-x-3">
        <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-lg text-gray-600 font-medium">Loading your tracks...</p>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="tracks.length === 0" class="text-center py-16">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 max-w-lg mx-auto">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-3">No tracks yet</h3>
        <p class="text-gray-600 mb-6">Start building your music collection by adding your first track.</p>
        <button
            @click="$emit('edit-track', null)"
            class="inline-flex items-center space-x-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
          </svg>
          <span>Add Your First Track</span>
        </button>
      </div>
    </div>

    <!-- Track Cards Grid -->
    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div
          v-for="track in tracks"
          :key="track.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-indigo-300 transition-all duration-300 overflow-hidden group"
      >
        <div class="p-6">
          <!-- Track Header -->
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1 min-w-0">
              <h3 class="text-lg font-bold text-gray-800 truncate group-hover:text-indigo-600 transition-colors">
                {{ track.title }}
              </h3>
              <p class="text-sm text-gray-600 truncate">{{ track.artist }}</p>
            </div>
            <button
                @click="editTrack(track)"
                class="ml-3 p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all duration-200 flex-shrink-0"
                title="Edit track"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
          </div>

          <!-- Track Details -->
          <div class="space-y-3">
            <div class="flex items-center space-x-2 text-sm">
              <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="text-gray-700 font-medium">{{ formatDuration(track.duration) }}</span>
            </div>

            <div v-if="track.isrc" class="flex items-center space-x-2 text-sm">
              <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
              </svg>
              <span class="text-gray-600 font-mono text-xs">{{ track.isrc }}</span>
            </div>
          </div>
        </div>

        <!-- Card Footer with Gradient -->
        <div class="h-1 bg-gradient-to-r from-indigo-500 to-purple-500 group-hover:from-indigo-600 group-hover:to-purple-600 transition-all"></div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useTrackStore } from '../stores/trackStore';

const emit = defineEmits(['edit-track']);

const trackStore = useTrackStore();

const tracks = computed(() => trackStore.tracks);
const loading = computed(() => trackStore.loading);

const totalDuration = computed(() => {
  if (tracks.value.length === 0) return '0:00';

  const totalSeconds = tracks.value.reduce((sum, track) => sum + track.duration, 0);
  const hours = Math.floor(totalSeconds / 3600);
  const minutes = Math.floor((totalSeconds % 3600) / 60);

  if (hours > 0) {
    return `${hours}h ${minutes}m`;
  }
  return `${minutes}m`;
});

onMounted(async () => {
  await trackStore.fetchTracks();
});

function formatDuration(seconds) {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function editTrack(track) {
  emit('edit-track', track);
}
</script>