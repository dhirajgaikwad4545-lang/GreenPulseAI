import numpy as np
import joblib
from sklearn.tree import DecisionTreeClassifier

X = np.array([
    [36, 0.5, 30, 18],
    [35, 0.6, 31, 21],
    [37, 0.7, 32, 26],
    [34, 0.8, 33, 27],

    [45, 0.5, 30, 22],
    [47, 0.6, 31, 28],
    [36, 3.5, 32, 126],
    [35, 4.0, 34, 140],

    [15, 0.8, 35, 12],
    [10, 0.7, 36, 7],
    [2, 0.1, 30, 0.2],
    [1, 0.0, 29, 0]
])

y = np.array([
    "NORMAL",
    "NORMAL",
    "NORMAL",
    "NORMAL",

    "OVERVOLTAGE",
    "OVERVOLTAGE",
    "OVERCURRENT",
    "OVERCURRENT",

    "LOW_GENERATION",
    "LOW_GENERATION",
    "FAULT",
    "FAULT"
])

model = DecisionTreeClassifier(
    random_state=42,
    max_depth=4
)

model.fit(X, y)

joblib.dump(model, "ai/model.pkl")

print("AI model trained successfully.")
print("Model saved as ai/model.pkl")