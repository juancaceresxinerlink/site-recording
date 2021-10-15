from pydub import AudioSegment
import sys
import logging
import logging.config
import os
import pandas as pd

audioBorrar = sys.argv[1]

os.remove(audioBorrar)