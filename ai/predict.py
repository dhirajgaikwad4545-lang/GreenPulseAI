import sys
import json
import joblib

model = joblib.load("ai/model.pkl")

voltage = float(sys.argv[1])
current = float(sys.argv[2])
temperature = float(sys.argv[3])

power = voltage * current

features = [[
    voltage,
    current,
    temperature,
    power
]]

prediction = model.predict(features)[0]

probability = model.predict_proba(features)[0]
confidence = max(probability) * 100

result = {
    "status": prediction,
    "confidence": round(confidence, 2),
    "voltage": voltage,
    "current": current,
    "temperature": temperature,
    "power": round(power, 2)
}

print(json.dumps(result))