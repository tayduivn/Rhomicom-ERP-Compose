REM CLEANUP RHO ERP
forfiles -p "src" -s -m *.pdf -c "cmd /c del @path"
forfiles -p "src" -s -m *.html -c "cmd /c del @path"
forfiles -p "src" -s -m desktop.ini -c "cmd /c del @path"