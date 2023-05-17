@echo off
chcp 437

rem echo %~dp0
rem dir /b %%~dp0\*sql

for /F %%i in ('dir /b %~dp0\*sql') do (
	echo %%i
	mysql -uroot truemetal_remote < %~dp0%%i
)
