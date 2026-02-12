#!/bin/bash
scanimage -d "$2" --format="pdf" --resolution="300" --mode="Color" > "$1/scan.pdf"