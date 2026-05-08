@echo off
REM Load environment variables from .env file
for /f "tokens=1,2 delims==" %%a in ('findstr /v "^#" .env') do (
    if "%%a"=="NGROK_TOKEN" set NGROK_TOKEN=%%b
    if "%%a"=="NGROK_DOMAIN" set NGROK_DOMAIN=%%b
)

REM Run ngrok with the variables
ngrok http localhost:8000 --url=https://%NGROK_DOMAIN% --authtoken=%NGROK_TOKEN%
