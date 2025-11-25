<template>
  <div class="relative">
    <!-- Modal Header -->
    <div class="sticky top-0 bg-white border-b border-gray-200 px-8 py-6 flex items-center justify-between z-10">
      <div>
        <h2 class="text-2xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
          {{ isEditing ? 'Edit Track' : 'Add New Track' }}
        </h2>
        <p class="text-sm text-gray-600 mt-1">{{ isEditing ? 'Update track information' : 'Fill in the details below' }}</p>
      </div>
      <button
          @click="handleCancel"
          class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors"
          title="Close"
      >
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>

    <!-- Modal Body -->
    <div class="px-8 py-6 space-y-6">
      <!-- Title Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2" for="title">
          Title
          <span class="text-red-500 ml-1">*</span>
        </label>
        <input
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none"
            :class="{ 'border-red-500 ring-2 ring-red-200': errors.title }"
            id="title"
            type="text"
            v-model="form.title"
            placeholder="Enter track title"
        >
        <p v-if="errors.title" class="mt-2 text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          {{ errors.title }}
        </p>
      </div>

      <!-- Artist Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2" for="artist">
          Artist
          <span class="text-red-500 ml-1">*</span>
        </label>
        <input
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none"
            :class="{ 'border-red-500 ring-2 ring-red-200': errors.artist }"
            id="artist"
            type="text"
            v-model="form.artist"
            placeholder="Enter artist name"
        >
        <p v-if="errors.artist" class="mt-2 text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          {{ errors.artist }}
        </p>
      </div>

      <!-- Duration Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2" for="duration">
          Duration
          <span class="text-red-500 ml-1">*</span>
        </label>
        <div class="relative">
          <input
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none"
              :class="{ 'border-red-500 ring-2 ring-red-200': errors.duration }"
              id="duration"
              type="text"
              v-model="durationFormatted"
              placeholder="mm:ss (e.g., 03:45)"
          >
          <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
        </div>
        <p v-if="errors.duration" class="mt-2 text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          {{ errors.duration }}
        </p>
        <p v-else class="mt-2 text-xs text-gray-500 flex items-center">
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          Format: minutes:seconds (00:00)
        </p>
      </div>

      <!-- ISRC Field -->
      <div>
        <label class="block text-sm font-semibold text-gray-700 mb-2" for="isrc">
          ISRC
          <span class="text-gray-400 text-xs font-normal ml-2">(Optional)</span>
        </label>
        <div class="relative">
          <input
              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 outline-none font-mono"
              :class="{ 'border-red-500 ring-2 ring-red-200': errors.isrc }"
              id="isrc"
              type="text"
              v-model="form.isrc"
              placeholder="XX-XXX-XX-XXXXX"
          >
          <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
            </svg>
          </div>
        </div>
        <p v-if="errors.isrc" class="mt-2 text-sm text-red-600 flex items-center">
          <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
          </svg>
          {{ errors.isrc }}
        </p>
        <p v-else class="mt-2 text-xs text-gray-500 flex items-center">
          <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
          </svg>
          Example: US-SME-21-12345
        </p>
      </div>
    </div>

    <!-- Modal Footer -->
    <div class="sticky bottom-0 bg-gray-50 border-t border-gray-200 px-8 py-6 flex items-center justify-end space-x-3">
      <button
          @click="handleCancel"
          class="px-6 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-100 transition-colors duration-200"
          type="button"
      >
        Cancel
      </button>
      <button
          @click="submitForm"
          :disabled="loading"
          class="px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none flex items-center space-x-2"
          type="button"
      >
        <svg v-if="loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span>{{ isEditing ? 'Update Track' : 'Add Track' }}</span>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useTrackStore } from '../stores/trackStore';

const trackStore = useTrackStore();

const props = defineProps({
  track: {
    type: Object,
    default: null
  }
});

const emit = defineEmits(['formSubmitted', 'cancel']);

const form = ref({
  id: null,
  title: '',
  artist: '',
  duration: 0,
  isrc: ''
});

const durationFormatted = ref('');
const errors = ref({});
const loading = computed(() => trackStore.loading);
const isEditing = computed(() => !!form.value.id);

// Initialize form with track data if provided
watch(() => props.track, (newTrack) => {
  if (newTrack) {
    form.value = { ...newTrack };

    // Convert duration from seconds to mm:ss format
    const minutes = Math.floor(newTrack.duration / 60);
    const seconds = newTrack.duration % 60;
    durationFormatted.value = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
  } else {
    resetForm();
  }
}, { immediate: true });

// Convert mm:ss to seconds when durationFormatted changes
watch(durationFormatted, (newValue) => {
  if (newValue) {
    const parts = newValue.split(':');
    if (parts.length === 2) {
      const minutes = parseInt(parts[0], 10) || 0;
      const seconds = parseInt(parts[1], 10) || 0;
      form.value.duration = (minutes * 60) + seconds;
    }
  } else {
    form.value.duration = 0;
  }
});

function validateForm() {
  const newErrors = {};

  if (!form.value.title || !form.value.title.trim()) {
    newErrors.title = 'Title is required';
  }

  if (!form.value.artist || !form.value.artist.trim()) {
    newErrors.artist = 'Artist is required';
  }

  if (!form.value.duration || form.value.duration <= 0) {
    newErrors.duration = 'Duration is required';
  }

  if (form.value.isrc && form.value.isrc.trim() && !/^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$/.test(form.value.isrc)) {
    newErrors.isrc = 'ISRC must match the format XX-XXX-XX-XXXXX';
  }

  errors.value = newErrors;
  return Object.keys(newErrors).length === 0;
}

async function submitForm() {
  if (!validateForm()) {
    return;
  }

  let result;

  if (isEditing.value) {
    result = await trackStore.updateTrack(form.value);
  } else {
    result = await trackStore.createTrack(form.value);
  }

  if (result.success) {
    resetForm();
    emit('formSubmitted');
  } else if (result.errors) {
    // Handle server-side validation errors
    const serverErrors = {};

    if (typeof result.errors === 'object') {
      Object.entries(result.errors).forEach(([key, value]) => {
        serverErrors[key] = Array.isArray(value) ? value[0] : value;
      });
    } else if (Array.isArray(result.errors)) {
      serverErrors.general = result.errors[0];
    }

    errors.value = { ...errors.value, ...serverErrors };
  }
}

function resetForm() {
  form.value = {
    id: null,
    title: '',
    artist: '',
    duration: 0,
    isrc: ''
  };
  durationFormatted.value = '';
  errors.value = {};
}

function handleCancel() {
  resetForm();
  trackStore.clearCurrentTrack();
  emit('cancel');
}
</script>