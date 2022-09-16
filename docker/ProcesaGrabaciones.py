from pydub import AudioSegment
import sys
import logging
import logging.config
import os
import pandas as pd

logger = logging.getLogger("INFO")
logger.setLevel(logging.DEBUG)
handler = logging.handlers.TimedRotatingFileHandler(
    filename="Grabaciones.log", when="d", interval=1, backupCount=5
)
formatter = logging.Formatter(
    fmt="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
    datefmt="%y-%m-%d %H:%M:%S",
)
handler.setFormatter(formatter)
logger.addHandler(handler)
audioTransformar = sys.argv[1]
audioSplit = audioTransformar.split(",")

contador = 0
# Arrays para ordenar
pathRecording = []
hora = []

for x in audioSplit:
    if x != "":
        split = x.split("_")
        _hora = split[5] + ":" + split[6] + ":" + split[7]
        hora.append(_hora)
        pathRecording.append(x)

    contador = contador + 1

# Generar DataFrame
df = pd.DataFrame({"path": pathRecording, "hora": hora})
print("*****************************")
df = df.sort_values("hora")
# DataFrame Ordenado luego hay que recorrer unir grabaciones y esta listo
print(df)
contar2 = 0
for path in df["path"]:
    print(contar2)
    if contar2 == 0:
        test = AudioSegment.from_wav(path)
    else:
        audio = AudioSegment.from_wav(path)
        test = test + audio
    contar2 = contar2 + 1

audioReturn = sys.argv[2]

logger.info("Audio ingresado a transformar" + audioReturn)
print("Sin extension " + audioReturn[:-3])
OutPutFile = audioReturn[:-3] + "mp3"
logger.info("NombreFinal " + OutPutFile)

test.export(OutPutFile, format="mp3")

os.remove(audioReturn)

for x in audioSplit:
    if x != "":
        os.remove(x)
