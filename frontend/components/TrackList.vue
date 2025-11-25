<template>
  <div>
    <!-- Loading State -->
    <div v-if="loading" class="flex items-center justify-center py-20">
      <div class="text-center">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-gray-200 border-t-indigo-600 mb-4"></div>
        <p class="text-gray-600 font-medium">Loading tracks...</p>
      </div>
    </div>

    <!-- Empty State -->
    <div v-else-if="!tracks || tracks.length === 0" class="max-w-4xl mx-auto">
      <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
          </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">No tracks yet</h3>
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

    <!-- Cards View -->
    <div v-else-if="viewMode === 'cards'" class="max-w-7xl mx-auto">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
            v-for="track in tracks"
            :key="track.id"
            class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg hover:border-indigo-300 transition-all duration-200 overflow-hidden group"
        >
          <!-- Card Header with Gradient -->
          <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-600"></div>

          <div class="p-6">
            <!-- Track Info -->
            <div class="mb-4">
              <h3 class="text-xl font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-indigo-600 transition-colors">
                {{ track.title }}
              </h3>
              <p class="text-gray-600 flex items-center">
                <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                {{ track.artist }}
              </p>
            </div>

            <!-- Track Details -->
            <div class="space-y-2 mb-4">
              <div class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-medium">Duration:</span>
                <span class="ml-2">{{ formatDuration(track.duration) }}</span>
              </div>
              <div v-if="track.isrc" class="flex items-center text-sm text-gray-600">
                <svg class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                </svg>
                <span class="font-medium">ISRC:</span>
                <span class="ml-2 font-mono text-xs">{{ track.isrc }}</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex space-x-2 pt-4 border-t border-gray-100">
              <button
                  @click="editTrack(track)"
                  class="flex-1 flex items-center justify-center space-x-2 px-4 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors duration-200 font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>Edit</span>
              </button>
              <button
                  @click="confirmDelete(track)"
                  class="flex-1 flex items-center justify-center space-x-2 px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200 font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
                <span>Delete</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Table View -->
    <div v-else class="max-w-7xl mx-auto">
      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead>
            <tr class="bg-gradient-to-r from-indigo-50 to-purple-50 border-b border-gray-200">
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Title
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Artist
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                Duration
              </th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">
                ISRC
              </th>
              <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">
                Actions
              </th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <tr
                v-for="track in tracks"
                :key="track.id"
                class="hover:bg-indigo-50/50 transition-colors duration-150"
            >
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                  <div class="w-2 h-10 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full mr-3"></div>
                  <div class="font-semibold text-gray-900">{{ track.title }}</div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-gray-700">
                {{ track.artist }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    {{ formatDuration(track.duration) }}
                  </span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                  <span v-if="track.isrc" class="font-mono text-sm text-gray-600 bg-gray-100 px-2 py-1 rounded">
                    {{ track.isrc }}
                  </span>
                <span v-else class="text-gray-400 text-sm italic">Not set</span>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right">
                <div class="flex items-center justify-end space-x-2">
                  <button
                      @click="editTrack(track)"
                      class="p-2 text-indigo-600 hover:bg-indigo-100 rounded-lg transition-colors duration-200"
                      title="Edit track"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                  </button>
                  <button
                      @click="confirmDelete(track)"
                      class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors duration-200"
                      title="Delete track"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                    </svg>
                  </button>
                </div>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Teleport to="body">
      <Transition
          enter-active-class="transition-opacity duration-300"
          leave-active-class="transition-opacity duration-200"
          enter-from-class="opacity-0"
          enter-to-class="opacity-100"
          leave-from-class="opacity-100"
          leave-to-class="opacity-0"
      >
        <div
            v-if="showDeleteConfirm"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="cancelDelete"
        >
          <Transition
              enter-active-class="transition-all duration-300"
              leave-active-class="transition-all duration-200"
              enter-from-class="opacity-0 scale-95"
              enter-to-class="opacity-100 scale-100"
              leave-from-class="opacity-100 scale-100"
              leave-to-class="opacity-0 scale-95"
          >
            <div
                v-if="showDeleteConfirm"
                class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6"
            >
              <div class="flex items-center justify-center w-12 h-12 mx-auto mb-4 bg-red-100 rounded-full">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
              </div>

              <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Track</h3>
              <p class="text-gray-600 text-center mb-6">
                Are you sure you want to delete "<span class="font-semibold">{{ trackToDelete?.title }}</span>"? This action cannot be undone.
              </p>

              <div class="flex space-x-3">
                <button
                    @click="cancelDelete"
                    class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200"
                >
                  Cancel
                </button>
                <button
                    @click="deleteTrack"
                    :disabled="deleting"
                    class="flex-1 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center space-x-2"
                >
                  <svg v-if="deleting" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>{{ deleting ? 'Deleting...' : 'Delete' }}</span>
                </button>
              </div>
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useTrackStore } from '../stores/trackStore';

const props = defineProps({
  viewMode: {
    type: String,
    default: 'cards',
    validator: (value) => ['cards', 'table'].includes(value)
  }
});

const emit = defineEmits(['edit-track']);

const trackStore = useTrackStore();

const tracks = computed(() => trackStore.tracks);
const loading = computed(() => trackStore.loading);

const showDeleteConfirm = ref(false);
const trackToDelete = ref(null);
const deleting = ref(false);

onMounted(() => {
  trackStore.fetchTracks();
});

function formatDuration(seconds) {
  const mins = Math.floor(seconds / 60);
  const secs = seconds % 60;
  return `${mins}:${secs.toString().padStart(2, '0')}`;
}

function editTrack(track) {
  emit('edit-track', track);
}

function confirmDelete(track) {
  trackToDelete.value = track;
  showDeleteConfirm.value = true;
}

function cancelDelete() {
  showDeleteConfirm.value = false;
  trackToDelete.value = null;
}

async function deleteTrack() {
  if (!trackToDelete.value) return;

  deleting.value = true;
  const result = await trackStore.deleteTrack(trackToDelete.value.id);
  deleting.value = false;

  if (result.success) {
    showDeleteConfirm.value = false;
    trackToDelete.value = null;
    trackStore.fetchTracks();
  }
}
</script>