:: Easy!Appointments
:: 
:: This scripts creates a new build for Easy!Appointments. This build
:: can be used for distributing a new version of the project.
TITLE Easy!Appointments
del /s/f/q "build\*.*"
for /f %%f in ('dir /ad /b "build"') do rd /s /q "build%f"
mkdir "build"
copy /y "release-notes.txt" "build\release-notes.txt"
xcopy /s/e/y "src" "build"
xcopy /y "build\configuration-sample.php" "build\configuration.php"
del "build\configuration-sample.php"