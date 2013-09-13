:: ===============================================
:: GENERATES THE EASY!APPOINTMENTS THESIS DOCUMENT
:: ===============================================

:: Run Cleanup
call:cleanupq
 
:: Run XeLaTex on main file.
xelatex thesis.tex --quiet
bibtex  thesis.tex

:: If you are using multibib the following will run bibtex on all aux files
:: FOR /R . %%G IN (*.aux) DO bibtex %%G
xelatex thesis.tex --quiet
 
:: Run Cleanup
call:cleanup
 
:: Open PDF (Script updated based on comments by 'menfeser'
:: START "" "C:\Program Files\Adobe\Reader 9.0\Reader\AcroRd32.exe" %2.pdf
START "" thesis.pdf
 
:: Cleanup Function
:cleanup
del *.log
del *.dvi
del *.aux
del *.bbl
del *.blg
del *.brf
del *.out
del *.log
del *.bcf
del *.xml
del *.toc

del includes\*.log
del includes\*.dvi
del includes\*.aux
del includes\*.bbl
del includes\*.blg
del includes\*.brf
del includes\*.out
del includes\*.log
del includes\*.bcf
del includes\*.xml
del includes\*.toc

goto:eof