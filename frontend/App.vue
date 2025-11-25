<template>
  <div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <!-- Header -->
    <header class="bg-white/80 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-40">
      <div class="container mx-auto px-4 py-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"></path>
              </svg>
            </div>
            <div>
              <h1 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                Track Manager
              </h1>
              <p class="text-sm text-gray-600">Manage your music collection</p>
            </div>
          </div>

          <div class="flex items-center space-x-3">
            <!-- View Toggle -->
            <div class="bg-gray-100 rounded-lg p-1 flex items-center space-x-1">
              <button
                  @click="viewMode = 'cards'"
                  :class="[
                    'px-3 py-2 rounded-md transition-all duration-200 flex items-center space-x-2',
                    viewMode === 'cards'
                      ? 'bg-white text-indigo-600 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                  title="Card View"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                </svg>
                <span class="text-sm font-medium hidden sm:inline">Cards</span>
              </button>
              <button
                  @click="viewMode = 'table'"
                  :class="[
                    'px-3 py-2 rounded-md transition-all duration-200 flex items-center space-x-2',
                    viewMode === 'table'
                      ? 'bg-white text-indigo-600 shadow-sm'
                      : 'text-gray-600 hover:text-gray-900'
                  ]"
                  title="Table View"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                <span class="text-sm font-medium hidden sm:inline">Table</span>
              </button>
            </div>

            <button
                @click="openModal"
                class="bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold py-2.5 px-6 rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 flex items-center space-x-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
              </svg>
              <span>Add Track</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
      <TrackList
          :view-mode="viewMode"
          @edit-track="handleEditTrack"
      />

      <!-- Error Message -->
      <div v-if="error" class="max-w-4xl mx-auto mt-4">
        <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
          <div class="flex items-center">
            <svg class="w-5 h-5 text-red-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-red-800 font-medium">{{ error }}</p>
          </div>
        </div>
      </div>
    </main>

    <!-- Modal -->
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
            v-if="isModalOpen"
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4"
            @click.self="closeModal"
        >
          <Transition
              enter-active-class="transition-all duration-300"
              leave-active-class="transition-all duration-200"
              enter-from-class="opacity-0 scale-95 translate-y-4"
              enter-to-class="opacity-100 scale-100 translate-y-0"
              leave-from-class="opacity-100 scale-100 translate-y-0"
              leave-to-class="opacity-0 scale-95 translate-y-4"
          >
            <div
                v-if="isModalOpen"
                class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
            >
              <TrackForm
                  :track="currentTrack"
                  @form-submitted="handleFormSubmitted"
                  @cancel="closeModal"
              />
            </div>
          </Transition>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useTrackStore } from './stores/trackStore';
import TrackForm from './components/TrackForm.vue';
import TrackList from './components/TrackList.vue';

const trackStore = useTrackStore();

const isModalOpen = ref(false);
const viewMode = ref('cards'); // 'cards' or 'table'
const currentTrack = computed(() => trackStore.currentTrack);
const error = computed(() => trackStore.error);

function openModal() {
  trackStore.clearCurrentTrack();
  isModalOpen.value = true;
}

function closeModal() {
  isModalOpen.value = false;
  trackStore.clearCurrentTrack();
}

function handleEditTrack(track) {
  trackStore.setCurrentTrack(track);
  isModalOpen.value = true;
}

function handleFormSubmitted() {
  trackStore.fetchTracks();
  closeModal();
}
</script>

<style>
/* Scrollbar styling */
::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

::-webkit-scrollbar-thumb {
  background: linear-gradient(180deg, #6366f1, #9333ea);
  border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(180deg, #4f46e5, #7e22ce);
}
</style>