<template>
  <div>
    <div v-if="loading" class="text-center py-4">
      <p class="text-gray-600">Loading tracks...</p>
    </div>
    
    <div v-else-if="tracks.length === 0" class="text-center py-4">
      <p class="text-gray-600">No tracks found. Add your first track using the form above.</p>
    </div>
    
    <div v-else class="overflow-x-auto">
      <table class="min-w-full bg-white">
        <thead>
          <tr>
            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Title
            </th>
            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Artist
            </th>
            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Duration
            </th>
            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              ISRC
            </th>
            <th class="py-2 px-4 border-b border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
              Actions
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="track in tracks" :key="track.id" class="hover:bg-gray-100">
            <td class="py-2 px-4 border-b border-gray-200">{{ track.title }}</td>
            <td class="py-2 px-4 border-b border-gray-200">{{ track.artist }}</td>
            <td class="py-2 px-4 border-b border-gray-200">{{ formatDuration(track.duration) }}</td>
            <td class="py-2 px-4 border-b border-gray-200">{{ track.isrc || '-' }}</td>
            <td class="py-2 px-4 border-b border-gray-200">
              <button 
                @click="editTrack(track)"
                class="text-blue-500 hover:text-blue-700 mr-2"
              >
                Edit
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useTrackStore } from '../stores/trackStore';

const trackStore = useTrackStore();

const tracks = computed(() => trackStore.tracks);
const loading = computed(() => trackStore.loading);

onMounted(async () => {
  await trackStore.fetchTracks();
});

function formatDuration(seconds) {
  const minutes = Math.floor(seconds / 60);
  const remainingSeconds = seconds % 60;
  return `${minutes.toString().padStart(2, '0')}:${remainingSeconds.toString().padStart(2, '0')}`;
}

function editTrack(track) {
  trackStore.setCurrentTrack(track);
  // Scroll to the form
  window.scrollTo({ top: 0, behavior: 'smooth' });
}
</script>