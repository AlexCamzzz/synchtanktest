<template>
  <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
    <h2 class="text-xl font-bold mb-4">{{ isEditing ? 'Edit Track' : 'Add New Track' }}</h2>
    
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
        Title *
      </label>
      <input 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.title }"
        id="title"
        type="text"
        v-model="form.title"
        placeholder="Track title"
      >
      <p v-if="errors.title" class="text-red-500 text-xs italic">{{ errors.title }}</p>
    </div>
    
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="artist">
        Artist *
      </label>
      <input 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.artist }"
        id="artist"
        type="text"
        v-model="form.artist"
        placeholder="Artist name"
      >
      <p v-if="errors.artist" class="text-red-500 text-xs italic">{{ errors.artist }}</p>
    </div>
    
    <div class="mb-4">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="duration">
        Duration (mm:ss) *
      </label>
      <input 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.duration }"
        id="duration"
        type="text"
        v-model="durationFormatted"
        placeholder="03:45"
      >
      <p v-if="errors.duration" class="text-red-500 text-xs italic">{{ errors.duration }}</p>
    </div>
    
    <div class="mb-6">
      <label class="block text-gray-700 text-sm font-bold mb-2" for="isrc">
        ISRC (optional)
      </label>
      <input 
        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        :class="{ 'border-red-500': errors.isrc }"
        id="isrc"
        type="text"
        v-model="form.isrc"
        placeholder="XX-XXX-XX-XXXXX"
      >
      <p v-if="errors.isrc" class="text-red-500 text-xs italic">{{ errors.isrc }}</p>
      <p class="text-gray-600 text-xs mt-1">Format: XX-XXX-XX-XXXXX (e.g., US-SME-21-12345)</p>
    </div>
    
    <div class="flex items-center justify-between">
      <button 
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        type="button"
        @click="submitForm"
        :disabled="loading"
      >
        {{ isEditing ? 'Update Track' : 'Add Track' }}
      </button>
      <button 
        v-if="isEditing"
        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline ml-2"
        type="button"
        @click="cancelEdit"
      >
        Cancel
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

const emit = defineEmits(['formSubmitted']);

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
    const [minutes, seconds] = newValue.split(':').map(part => parseInt(part, 10) || 0);
    form.value.duration = (minutes * 60) + seconds;
  } else {
    form.value.duration = 0;
  }
});

function validateForm() {
  const newErrors = {};
  
  if (!form.value.title) {
    newErrors.title = 'Title is required';
  }
  
  if (!form.value.artist) {
    newErrors.artist = 'Artist is required';
  }
  
  if (!form.value.duration) {
    newErrors.duration = 'Duration is required';
  }
  
  if (form.value.isrc && !/^[A-Z]{2}-[A-Z0-9]{3}-\d{2}-\d{5}$/.test(form.value.isrc)) {
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
      // If errors is an array, show the first error in a generic way
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

function cancelEdit() {
  resetForm();
  trackStore.clearCurrentTrack();
}
</script>