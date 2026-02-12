#!/bin/bash
scanimage -d "$2" --format="pdf" --batch="$1/scan.pdf" --source="ADF Duplex" --resolution="300" --mode="Color" --batch-start="1" --batch-count="9999"