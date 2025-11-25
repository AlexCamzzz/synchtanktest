
# Track Manager Application Summary

## 1. Project Overview
A full-stack web application for managing music collections.  
The system features a decoupled architecture with a Symfony REST API for data persistence and a Vue.js 3 interface for user interaction.

## 2. Technical Stack

### Backend
- Symfony 6+
- Doctrine ORM  
- PHP 8.1+

### Frontend
- Vue.js 3 (Composition API)
- Pinia (State Management)
- Tailwind CSS

### Communication
- Axios (HTTP Client)
- Custom CORS handling

## 3. Backend Architecture (Symfony)

The backend follows a Service–Repository pattern to keep business logic separated from controllers.

### Core Components

#### Entity (`Track.php`)
- Defines the schema with fields: `id`, `title`, `artist`, `duration` (integer), and `isrc`.
- Validation rules:
  - `NotBlank` for required fields
  - `Positive` for duration
  - Regex for ISRC format

#### Repository (`TrackRepository.php`)
- Extends `ServiceEntityRepository`.
- Includes:
  - `searchByTitleOrArtist`
  - `findByMinDuration`
  - `getTotalDuration`

#### Service (`TrackService.php`)
- Acts as middleware between controller and repository.
- Handles creation and update logic.
- Converts Symfony validator errors into clean arrays for controllers.

#### Controller (`TrackController.php`)
- Exposes:
  - `GET /api/tracks`
  - `POST /api/tracks`
  - `PUT /api/tracks/{id}`
- Returns strictly typed `JsonResponse` objects.
- Provides correct HTTP status codes (201, 400, 404).

### Infrastructure

#### CORS Listener (`CorsListener.php`)
A custom Event Listener used to solve cross-origin issues between ports 8000 (backend) and 5173 (frontend).

Behavior:
- On `kernel.request`: handles OPTIONS preflight requests immediately.
- On `kernel.response`: appends `Access-Control-Allow-Origin: *` and related headers.

## 4. Frontend Architecture (Vue.js)

Component-based architecture with global state managed by Pinia.

### State Management (`trackStore.js`)
State:
- `tracks`
- `currentTrack`
- `loading`
- `error`

Actions:
- `fetchTracks()`  
- `createTrack()` / `updateTrack()`  
- `deleteTrack()` (prepared for future use)

### Components

#### Entry Point (`main.js`)
- Initializes Vue app
- Mounts `App.vue`
- Installs Pinia

#### Main Layout (`App.vue`)
- Manages application structure
- Handles modal visibility via Teleport
- Contains Header and TrackList view

#### Track List (`TrackList.vue`)
- Renders tracks in a responsive grid
- Displays total duration dynamically
- Handles loading and empty states

#### Track Form (`TrackForm.vue`)
- Reusable for adding and editing tracks
- Converts duration:
  - mm:ss → seconds (backend)
  - seconds → mm:ss (frontend)
- Displays inline validation errors from API

## 5. File Structure Map

```
Project Root
├── backend/ (Symfony)
│   ├── src/
│   │   ├── Controller/
│   │   │   └── TrackController.php
│   │   ├── Entity/
│   │   │   └── Track.php
│   │   ├── EventListener/
│   │   │   └── CorsListener.php
│   │   ├── Repository/
│   │   │   └── TrackRepository.php
│   │   └── Service/
│   │       └── TrackService.php
│   └── .env
│
└── frontend/ (Vue)
    ├── src/
    │   ├── components/
    │   │   ├── TrackForm.vue
    │   │   └── TrackList.vue
    │   ├── stores/
    │   │   └── trackStore.js
    │   ├── App.vue
    │   └── main.js
    └── index.html
```

## 6. API Reference

| Method | Endpoint             | Description           | Payload Example                                   |
|--------|-----------------------|------------------------|---------------------------------------------------|
| GET    | `/api/tracks`         | Retrieve all tracks   | N/A                                               |
| POST   | `/api/tracks`         | Create a track        | `{ "title": "Song", "artist": "Name", "duration": 180 }` |
| PUT    | `/api/tracks/{id}`    | Update a track        | `{ "title": "New Title", "duration": 200 }`       |

## 7. Setup & Running

### Backend (Port 8000)

```bash
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
symfony serve
```

### Frontend (Port 5173)

```bash
npm install
npm run dev
```
