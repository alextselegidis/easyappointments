#!/bin/sh

## ===============================================
## GENERATES THE EASY!APPOINTMENTS THESIS DOCUMENT
## ===============================================

clear
xelatex --output-format=pdf thesis.tex --quiet
bibtex  thesis.tex
xelatex --output-format=pdf thesis.tex --quiet

rm *.log
rm *.dvi
rm *.aux
rm *.bbl
rm *.blg
rm *.brf
rm *.out
rm *.log
rm *.bcf
rm *.xml
rm *.toc

rm includes\*.log
rm includes\*.dvi
rm includes\*.aux
rm includes\*.bbl
rm includes\*.blg
rm includes\*.brf
rm includes\*.out
rm includes\*.log
rm includes\*.bcf
rm includes\*.xml
rm includes\*.toc

echo "Process Ended"
