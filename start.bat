@echo off
echo 🚀 Starting Track Management App...

REM --- BACKEND ---
IF EXIST "backend" (
    echo 🔌 Starting Backend...
    cd backend

    REM Check for vendor folder
    IF NOT EXIST "vendor" (
        echo Installing backend dependencies...
        call composer install
    )

    REM Start Symfony server in daemon mode
    call symfony server:start -d --no-tls
    cd ..
) ELSE (
    echo ❌ Backend directory not found!
    pause
    exit /b 1
)

REM --- FRONTEND ---
IF EXIST "frontend" (
    echo 💻 Starting Frontend...
    cd frontend

    REM Check for node_modules
    IF NOT EXIST "node_modules" (
        echo Installing frontend dependencies...
        call npm install
    )

    echo Starting Vite...
    call npm run dev

    REM When npm run dev finishes (Ctrl+C), stop the backend
    echo.
    echo 🛑 Stopping Backend...
    cd ../backend
    call symfony server:stop
    cd ..
) ELSE (
    echo ❌ Frontend directory not found!
    cd backend
    call symfony server:stop
    pause
)